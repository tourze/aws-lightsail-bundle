<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Integration\Command;

use AwsLightsailBundle\Command\InstanceCreateCommand;
use AwsLightsailBundle\Repository\AwsCredentialRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class InstanceCreateCommandTest extends TestCase
{
    private AwsCredentialRepository $credentialRepository;
    private InstanceCreateCommand $command;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->credentialRepository = $this->createMock(AwsCredentialRepository::class);
        
        $this->command = new InstanceCreateCommand(
            $this->credentialRepository
        );

        $application = new Application();
        $application->add($this->command);
        $this->commandTester = new CommandTester($this->command);
    }

    public function testCommand_hasCorrectName(): void
    {
        $this->assertSame(InstanceCreateCommand::NAME, $this->command->getName());
    }

    public function testCommand_isInstanceOfCommand(): void
    {
        $this->assertInstanceOf(Command::class, $this->command);
    }

    public function testCommand_hasCorrectDescription(): void
    {
        $this->assertSame('创建 AWS Lightsail 实例', $this->command->getDescription());
    }

    public function testConstantName_hasCorrectValue(): void
    {
        $this->assertSame('aws:lightsail:instance:create', InstanceCreateCommand::NAME);
    }

    public function testExecute_withNoCredentials_returnsFailure(): void
    {
        $this->credentialRepository
            ->method('findAll')
            ->willReturn([]);

        $this->commandTester->execute([
            'name' => 'test-instance',
        ]);

        $this->assertSame(Command::FAILURE, $this->commandTester->getStatusCode());
        $this->assertStringContainsString('未找到任何 AWS 凭证', $this->commandTester->getDisplay());
    }

    public function testConfigure_setsCorrectArguments(): void
    {
        $definition = $this->command->getDefinition();
        
        $this->assertTrue($definition->hasArgument('name'));
        
        $instanceNameArg = $definition->getArgument('name');
        $this->assertTrue($instanceNameArg->isRequired());
    }

    public function testConfigure_setsCorrectOptions(): void
    {
        $definition = $this->command->getDefinition();
        
        $this->assertTrue($definition->hasOption('credential-id'));
        $this->assertTrue($definition->hasOption('region'));
        $this->assertTrue($definition->hasOption('blueprint'));
        $this->assertTrue($definition->hasOption('bundle'));
        $this->assertTrue($definition->hasOption('availability-zone'));
        $this->assertTrue($definition->hasOption('key-pair-name'));
        $this->assertTrue($definition->hasOption('tags'));
        $this->assertTrue($definition->hasOption('user-data'));
    }
}