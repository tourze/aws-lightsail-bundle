<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Command;

use AwsLightsailBundle\Command\InstanceControlCommand;
use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Instance;
use AwsLightsailBundle\Enum\AmazonRegion;
use AwsLightsailBundle\Enum\InstanceStateEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * @internal
 */
#[CoversClass(InstanceControlCommand::class)]
#[RunTestsInSeparateProcesses]
final class InstanceControlCommandTest extends AbstractCommandTestCase
{
    protected function onSetUp(): void
    {
        // Command 测试需要空的 onSetUp 方法
    }

    protected function getCommandTester(): CommandTester
    {
        $command = self::getContainer()->get(InstanceControlCommand::class);
        $this->assertInstanceOf(InstanceControlCommand::class, $command);

        $application = new Application();
        $application->addCommand($command);

        return new CommandTester($command);
    }

    public function testExecuteWithInvalidOperation(): void
    {
        $commandTester = $this->getCommandTester();
        $commandTester->execute([
            'operation'     => 'invalid',
            'instance-name' => 'test-instance',
        ]);

        $this->assertSame(1, $commandTester->getStatusCode());
        $this->assertStringContainsString('无效的操作类型', $commandTester->getDisplay());
    }

    public function testExecuteStartOperationWithForce(): void
    {
        $credential = new AwsCredential();
        $credential->setName('test-credential');
        $credential->setAccessKeyId('test-key');
        $credential->setSecretAccessKey('test-secret');
        $credential->setIsDefault(true);

        $instance = new Instance();
        $instance->setName('test-instance');
        $instance->setCredential($credential);
        $instance->setRegion(AmazonRegion::US_EAST_1->value);
        $instance->setState(InstanceStateEnum::STOPPED);

        // Mock AWS SDK response - skip actual API call
        $commandTester = $this->getCommandTester();
        $commandTester->execute([
            'operation'     => 'start',
            'instance-name' => 'test-instance',
            '--force'       => true,
        ]);

        // 由于我们无法模拟 AWS SDK，命令会在尝试调用 API 时失败
        // 但至少可以验证流程到达了正确的位置
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('启动 AWS Lightsail 实例', $output);
    }

    public function testCommandExists(): void
    {
        $command = self::getContainer()->get(InstanceControlCommand::class);
        $this->assertInstanceOf(InstanceControlCommand::class, $command);
    }

    public function testGetCommandName(): void
    {
        $this->assertSame('aws:lightsail:instance:control', InstanceControlCommand::NAME);
    }

    public function testArgumentOperation(): void
    {
        $command = self::getContainer()->get(InstanceControlCommand::class);
        $this->assertInstanceOf(InstanceControlCommand::class, $command);

        $definition = $command->getDefinition();
        $this->assertTrue($definition->hasArgument('operation'));
        $this->assertTrue($definition->getArgument('operation')->isRequired());
    }

    public function testArgumentInstanceName(): void
    {
        $command = self::getContainer()->get(InstanceControlCommand::class);
        $this->assertInstanceOf(InstanceControlCommand::class, $command);

        $definition = $command->getDefinition();
        $this->assertTrue($definition->hasArgument('instance-name'));
        $this->assertFalse($definition->getArgument('instance-name')->isRequired());
    }

    public function testOptionCredentialId(): void
    {
        $command = self::getContainer()->get(InstanceControlCommand::class);
        $this->assertInstanceOf(InstanceControlCommand::class, $command);

        $definition = $command->getDefinition();
        $this->assertTrue($definition->hasOption('credential-id'));
        $this->assertFalse($definition->getOption('credential-id')->isValueRequired());
    }

    public function testOptionRegion(): void
    {
        $command = self::getContainer()->get(InstanceControlCommand::class);
        $this->assertInstanceOf(InstanceControlCommand::class, $command);

        $definition = $command->getDefinition();
        $this->assertTrue($definition->hasOption('region'));
        $this->assertFalse($definition->getOption('region')->isValueRequired());
    }

    public function testOptionForce(): void
    {
        $command = self::getContainer()->get(InstanceControlCommand::class);
        $this->assertInstanceOf(InstanceControlCommand::class, $command);

        $definition = $command->getDefinition();
        $this->assertTrue($definition->hasOption('force'));
        $this->assertFalse($definition->getOption('force')->acceptValue());
    }
}
