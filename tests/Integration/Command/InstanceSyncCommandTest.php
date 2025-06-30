<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Integration\Command;

use AwsLightsailBundle\Command\InstanceSyncCommand;
use AwsLightsailBundle\Repository\AwsCredentialRepository;
use AwsLightsailBundle\Service\InstanceSyncService;
use AwsLightsailBundle\Service\KeyPairSyncService;
use Psr\Log\LoggerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class InstanceSyncCommandTest extends TestCase
{
    private AwsCredentialRepository $credentialRepository;
    private InstanceSyncService $instanceSyncService;
    private KeyPairSyncService $keyPairSyncService;
    private LoggerInterface $logger;
    private InstanceSyncCommand $command;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->credentialRepository = $this->createMock(AwsCredentialRepository::class);
        $this->instanceSyncService = $this->createMock(InstanceSyncService::class);
        $this->keyPairSyncService = $this->createMock(KeyPairSyncService::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        
        $this->command = new InstanceSyncCommand(
            $this->credentialRepository,
            $this->instanceSyncService,
            $this->keyPairSyncService,
            $this->logger
        );

        $application = new Application();
        $application->add($this->command);
        $this->commandTester = new CommandTester($this->command);
    }

    public function testCommand_hasCorrectName(): void
    {
        $this->assertSame(InstanceSyncCommand::NAME, $this->command->getName());
    }

    public function testCommand_isInstanceOfCommand(): void
    {
        $this->assertInstanceOf(Command::class, $this->command);
    }

    public function testCommand_hasCorrectDescription(): void
    {
        $this->assertSame('同步 AWS Lightsail 实例列表', $this->command->getDescription());
    }

    public function testConstantName_hasCorrectValue(): void
    {
        $this->assertSame('aws:lightsail:instance:sync', InstanceSyncCommand::NAME);
    }

    public function testExecute_callsSyncService(): void
    {
        $this->instanceSyncService
            ->expects($this->once())
            ->method('syncAllInstances');

        $this->commandTester->execute([]);

        $this->assertSame(Command::SUCCESS, $this->commandTester->getStatusCode());
    }

    public function testConfigure_setsCorrectOptions(): void
    {
        $definition = $this->command->getDefinition();
        
        $this->assertTrue($definition->hasOption('credential-id'));
        $this->assertTrue($definition->hasOption('region'));
        $this->assertTrue($definition->hasOption('force'));
        
        $forceOption = $definition->getOption('force');
        $this->assertFalse($forceOption->acceptValue());
    }
}