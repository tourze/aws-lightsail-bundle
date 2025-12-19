<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Command;

use AwsLightsailBundle\Command\InstanceCreateCommand;
use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Repository\AwsCredentialRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * @internal
 */
#[CoversClass(InstanceCreateCommand::class)]
#[RunTestsInSeparateProcesses]
final class InstanceCreateCommandWithDbTest extends AbstractCommandTestCase
{
    protected function getCommandTester(): CommandTester
    {
        $command = self::getContainer()->get(InstanceCreateCommand::class);
        $this->assertInstanceOf(InstanceCreateCommand::class, $command);

        $application = new Application();
        $application->addCommand($command);

        return new CommandTester($command);
    }

    protected function onSetUp(): void
    {
        // 集成测试需要空的 onSetUp 方法
    }

    public function testExecuteWithValidInstanceNameShouldStartCreationFlow(): void
    {
        // 创建一个测试凭证
        $credentialRepository = self::getService(AwsCredentialRepository::class);
        $credential           = new AwsCredential();
        $credential->setName('test-credential');
        $credential->setAccessKeyId('AKIA...');
        $credential->setSecretAccessKey('secret-key');
        $credential->setIsDefault(true);
        $credentialRepository->save($credential, true);

        $commandTester = $this->getCommandTester();

        $exitCode = $commandTester->execute([
            'name'            => 'test-instance',
            '--credential-id' => (string) $credential->getId(),
            '--region'        => 'us-east-1',
            '--blueprint'     => 'ubuntu_20_04',
            '--bundle'        => 'micro_2_0',
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('创建 AWS Lightsail 实例', $output);
        $this->assertStringContainsString('将使用以下配置创建实例', $output);
        $this->assertStringContainsString('test-instance', $output);
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
