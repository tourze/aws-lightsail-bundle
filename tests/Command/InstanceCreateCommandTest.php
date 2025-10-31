<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Command;

use AwsLightsailBundle\Command\InstanceCreateCommand;
use AwsLightsailBundle\Repository\AwsCredentialRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * @internal
 */
#[CoversClass(InstanceCreateCommand::class)]
#[RunTestsInSeparateProcesses]
final class InstanceCreateCommandTest extends AbstractCommandTestCase
{
    protected function onSetUp(): void
    {
        // 集成测试需要空的 onSetUp 方法
    }

    protected function getCommandTester(): CommandTester
    {
        /** @var InstanceCreateCommand $command */
        $command = self::getContainer()->get(InstanceCreateCommand::class);
        $this->assertInstanceOf(InstanceCreateCommand::class, $command);

        $application = new Application();
        $application->add($command);

        return new CommandTester($command);
    }

    public function testCommandExists(): void
    {
        /** @var InstanceCreateCommand $command */
        $command = self::getContainer()->get(InstanceCreateCommand::class);
        $this->assertInstanceOf(InstanceCreateCommand::class, $command);
    }

    public function testGetCommandName(): void
    {
        $this->assertSame('aws:lightsail:instance:create', InstanceCreateCommand::NAME);
    }

    public function testCommandConfigurationHasRequiredArgument(): void
    {
        /** @var InstanceCreateCommand $command */
        $command = self::getContainer()->get(InstanceCreateCommand::class);
        $this->assertInstanceOf(InstanceCreateCommand::class, $command);

        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasArgument('name'));
        $this->assertTrue($definition->getArgument('name')->isRequired());
        $this->assertEquals('实例名称', $definition->getArgument('name')->getDescription());
    }

    public function testCommandConfigurationHasExpectedOptions(): void
    {
        /** @var InstanceCreateCommand $command */
        $command = self::getContainer()->get(InstanceCreateCommand::class);
        $this->assertInstanceOf(InstanceCreateCommand::class, $command);

        $definition = $command->getDefinition();

        $expectedOptions = [
            'credential-id',
            'region',
            'blueprint',
            'bundle',
            'availability-zone',
            'key-pair-name',
            'tags',
            'user-data',
        ];

        foreach ($expectedOptions as $option) {
            $this->assertTrue($definition->hasOption($option), "Option '{$option}' should exist");
        }
    }

    public function testExecuteWithoutInstanceNameShouldFail(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments');

        $commandTester = $this->getCommandTester();
        $commandTester->execute([]);
    }

    public function testArgumentName(): void
    {
        $command = self::getContainer()->get(InstanceCreateCommand::class);
        $this->assertInstanceOf(InstanceCreateCommand::class, $command);

        $definition = $command->getDefinition();
        $this->assertTrue($definition->hasArgument('name'));
        $this->assertTrue($definition->getArgument('name')->isRequired());
    }

    public function testOptionCredentialId(): void
    {
        $command = self::getContainer()->get(InstanceCreateCommand::class);
        $this->assertInstanceOf(InstanceCreateCommand::class, $command);

        $definition = $command->getDefinition();
        $this->assertTrue($definition->hasOption('credential-id'));
        $this->assertFalse($definition->getOption('credential-id')->isValueRequired());
    }

    public function testOptionRegion(): void
    {
        $command = self::getContainer()->get(InstanceCreateCommand::class);
        $this->assertInstanceOf(InstanceCreateCommand::class, $command);

        $definition = $command->getDefinition();
        $this->assertTrue($definition->hasOption('region'));
        $this->assertFalse($definition->getOption('region')->isValueRequired());
    }

    public function testOptionBlueprint(): void
    {
        $command = self::getContainer()->get(InstanceCreateCommand::class);
        $this->assertInstanceOf(InstanceCreateCommand::class, $command);

        $definition = $command->getDefinition();
        $this->assertTrue($definition->hasOption('blueprint'));
        $this->assertFalse($definition->getOption('blueprint')->isValueRequired());
    }

    public function testOptionBundle(): void
    {
        $command = self::getContainer()->get(InstanceCreateCommand::class);
        $this->assertInstanceOf(InstanceCreateCommand::class, $command);

        $definition = $command->getDefinition();
        $this->assertTrue($definition->hasOption('bundle'));
        $this->assertFalse($definition->getOption('bundle')->isValueRequired());
    }

    public function testOptionAvailabilityZone(): void
    {
        $command = self::getContainer()->get(InstanceCreateCommand::class);
        $this->assertInstanceOf(InstanceCreateCommand::class, $command);

        $definition = $command->getDefinition();
        $this->assertTrue($definition->hasOption('availability-zone'));
        $this->assertFalse($definition->getOption('availability-zone')->isValueRequired());
    }

    public function testOptionKeyPairName(): void
    {
        $command = self::getContainer()->get(InstanceCreateCommand::class);
        $this->assertInstanceOf(InstanceCreateCommand::class, $command);

        $definition = $command->getDefinition();
        $this->assertTrue($definition->hasOption('key-pair-name'));
        $this->assertFalse($definition->getOption('key-pair-name')->isValueRequired());
    }

    public function testOptionTags(): void
    {
        $command = self::getContainer()->get(InstanceCreateCommand::class);
        $this->assertInstanceOf(InstanceCreateCommand::class, $command);

        $definition = $command->getDefinition();
        $this->assertTrue($definition->hasOption('tags'));
        $this->assertFalse($definition->getOption('tags')->isValueRequired());
    }

    public function testOptionUserData(): void
    {
        $command = self::getContainer()->get(InstanceCreateCommand::class);
        $this->assertInstanceOf(InstanceCreateCommand::class, $command);

        $definition = $command->getDefinition();
        $this->assertTrue($definition->hasOption('user-data'));
        $this->assertFalse($definition->getOption('user-data')->isValueRequired());
    }
}
