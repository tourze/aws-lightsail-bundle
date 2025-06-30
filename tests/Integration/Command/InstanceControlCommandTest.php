<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Integration\Command;

use AwsLightsailBundle\Command\InstanceControlCommand;
use AwsLightsailBundle\Repository\AwsCredentialRepository;
use AwsLightsailBundle\Repository\InstanceRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class InstanceControlCommandTest extends TestCase
{
    private InstanceRepository $instanceRepository;
    private AwsCredentialRepository $credentialRepository;
    private InstanceControlCommand $command;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->instanceRepository = $this->createMock(InstanceRepository::class);
        $this->credentialRepository = $this->createMock(AwsCredentialRepository::class);
        
        $this->command = new InstanceControlCommand(
            $this->instanceRepository,
            $this->credentialRepository
        );

        $application = new Application();
        $application->add($this->command);
        $this->commandTester = new CommandTester($this->command);
    }

    public function testCommand_hasCorrectName(): void
    {
        $this->assertSame(InstanceControlCommand::NAME, $this->command->getName());
    }

    public function testCommand_isInstanceOfCommand(): void
    {
        $this->assertInstanceOf(Command::class, $this->command);
    }

    public function testCommand_hasCorrectDescription(): void
    {
        $this->assertSame('控制 AWS Lightsail 实例（启动/停止/重启）', $this->command->getDescription());
    }

    public function testExecute_withInvalidOperation_returnsFailure(): void
    {
        $this->commandTester->execute([
            'operation' => 'invalid',
        ]);

        $this->assertSame(Command::FAILURE, $this->commandTester->getStatusCode());
        $this->assertStringContainsString('无效的操作类型', $this->commandTester->getDisplay());
    }

    public function testExecute_withValidOperations_acceptsStartStopReboot(): void
    {
        $validOperations = ['start', 'stop', 'reboot'];
        
        foreach ($validOperations as $operation) {
            $this->credentialRepository
                ->method('findAll')
                ->willReturn([]);

            $commandTester = new CommandTester($this->command);
            $commandTester->execute([
                'operation' => $operation,
                'instance-name' => 'test-instance',
            ]);

            // Should not fail with invalid operation error
            $display = $commandTester->getDisplay();
            $this->assertStringNotContainsString('无效的操作类型', $display);
        }
    }

    public function testExecute_withNoCredentials_returnsFailure(): void
    {
        $this->instanceRepository
            ->method('findOneBy')
            ->willReturn(null);
            
        $this->credentialRepository
            ->method('findAll')
            ->willReturn([]);

        $this->commandTester->execute([
            'operation' => 'start',
            'instance-name' => 'test-instance',
        ]);

        $this->assertSame(Command::FAILURE, $this->commandTester->getStatusCode());
        $this->assertStringContainsString('未找到任何 AWS 凭证', $this->commandTester->getDisplay());
    }

    public function testExecute_withForceOption_skipsConfirmation(): void
    {
        $this->instanceRepository
            ->method('findOneBy')
            ->willReturn(null);
            
        $this->credentialRepository
            ->method('findAll')
            ->willReturn([]);

        $this->commandTester->execute([
            'operation' => 'start',
            'instance-name' => 'test-instance',
            '--force' => true,
        ]);

        // Should reach credential error without confirmation prompt
        $this->assertStringContainsString('未找到任何 AWS 凭证', $this->commandTester->getDisplay());
    }

    public function testConstantName_hasCorrectValue(): void
    {
        $this->assertSame('aws:lightsail:instance:control', InstanceControlCommand::NAME);
    }

    public function testConfigure_setsCorrectArguments(): void
    {
        $definition = $this->command->getDefinition();
        
        $this->assertTrue($definition->hasArgument('operation'));
        $this->assertTrue($definition->hasArgument('instance-name'));
        
        $operationArg = $definition->getArgument('operation');
        $this->assertTrue($operationArg->isRequired());
        
        $instanceNameArg = $definition->getArgument('instance-name');
        $this->assertFalse($instanceNameArg->isRequired());
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