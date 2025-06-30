<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Command;

use AwsLightsailBundle\Command\InstanceSyncCommand;
use AwsLightsailBundle\Repository\AwsCredentialRepository;
use AwsLightsailBundle\Service\InstanceSyncService;
use AwsLightsailBundle\Service\KeyPairSyncService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

final class InstanceSyncCommandTest extends TestCase
{
    private CommandTester $commandTester;
    
    /** @var MockObject */
    private MockObject $credentialRepository;
    
    /** @var MockObject */
    private MockObject $instanceSyncService;
    
    /** @var MockObject */
    private MockObject $keyPairSyncService;
    
    /** @var MockObject */
    private MockObject $logger;

    protected function setUp(): void
    {
        $this->credentialRepository = $this->createMock(AwsCredentialRepository::class);
        $this->instanceSyncService = $this->createMock(InstanceSyncService::class);
        $this->keyPairSyncService = $this->createMock(KeyPairSyncService::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $command = new InstanceSyncCommand(
            $this->credentialRepository,
            $this->instanceSyncService,
            $this->keyPairSyncService,
            $this->logger
        );

        $application = new Application();
        $application->add($command);

        $this->commandTester = new CommandTester($command);
    }

    public function testExecuteWithNoCredentials(): void
    {
        $this->credentialRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $this->commandTester->execute([]);

        $this->assertSame(1, $this->commandTester->getStatusCode());
        $this->assertStringContainsString('未找到任何 AWS 凭证', $this->commandTester->getDisplay());
    }

    public function testGetCommandName(): void
    {
        $this->assertSame('aws:lightsail:instance:sync', InstanceSyncCommand::NAME);
    }
}