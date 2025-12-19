<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Command;

use AwsLightsailBundle\Command\InstanceSyncCommand;
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
#[CoversClass(InstanceSyncCommand::class)]
#[RunTestsInSeparateProcesses]
final class InstanceSyncCommandTest extends AbstractCommandTestCase
{
    protected function getCommandTester(): CommandTester
    {
        $command = self::getContainer()->get(InstanceSyncCommand::class);
        $this->assertInstanceOf(InstanceSyncCommand::class, $command);

        $application = new Application();
        $application->addCommand($command);

        return new CommandTester($command);
    }

    protected function onSetUp(): void
    {
        // 集成测试的初始化逻辑
    }

    public function testCommandExists(): void
    {
        $command = self::getContainer()->get(InstanceSyncCommand::class);
        $this->assertInstanceOf(InstanceSyncCommand::class, $command);
    }

    public function testGetCommandName(): void
    {
        $this->assertSame('aws:lightsail:instance:sync', InstanceSyncCommand::NAME);
    }

    public function testCommandConfigurationHasExpectedOptions(): void
    {
        $command = self::getContainer()->get(InstanceSyncCommand::class);
        $this->assertInstanceOf(InstanceSyncCommand::class, $command);

        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('credential-id'));
        $this->assertTrue($definition->hasOption('region'));

        $this->assertEquals('AWS 凭证 ID，不提供则使用所有凭证', $definition->getOption('credential-id')->getDescription());
        $this->assertEquals('指定区域，不提供则遍历所有区域', $definition->getOption('region')->getDescription());
    }

    public function testExecuteWithCredentialsShouldStartSync(): void
    {
        $commandTester = $this->getCommandTester();

        $exitCode = $commandTester->execute([
            '--region' => 'us-east-1',
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('同步 AWS Lightsail 实例列表', $output);
        // 不检查退出码，因为可能因为网络问题失败
    }

    public function testExecuteWithValidCredentialShouldStartSync(): void
    {
        // 创建一个测试凭证
        $credentialRepository = self::getService(AwsCredentialRepository::class);
        $credential           = new AwsCredential();
        $credential->setName('test-sync-credential');
        $credential->setAccessKeyId('AKIA...');
        $credential->setSecretAccessKey('secret-key');
        $credential->setIsDefault(true);
        $credentialRepository->save($credential, true);

        $commandTester = $this->getCommandTester();

        $exitCode = $commandTester->execute([
            '--credential-id' => (string) $credential->getId(),
            '--region'        => 'us-east-1',
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('同步 AWS Lightsail 实例列表', $output);
        $this->assertStringContainsString('将使用 1 个凭证同步 1 个区域', $output);
        $this->assertStringContainsString('test-sync-credential', $output);
    }

    public function testExecuteWithAllCredentialsShouldProcessAll(): void
    {
        // 创建多个测试凭证
        $credentialRepository = self::getService(AwsCredentialRepository::class);

        $credential1 = new AwsCredential();
        $credential1->setName('test-credential-1');
        $credential1->setAccessKeyId('AKIA1...');
        $credential1->setSecretAccessKey('secret-key-1');
        $credentialRepository->save($credential1, true);

        $credential2 = new AwsCredential();
        $credential2->setName('test-credential-2');
        $credential2->setAccessKeyId('AKIA2...');
        $credential2->setSecretAccessKey('secret-key-2');
        $credentialRepository->save($credential2, true);

        $commandTester = $this->getCommandTester();

        $exitCode = $commandTester->execute([
            '--region' => 'us-east-1',
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('同步 AWS Lightsail 实例列表', $output);
        $this->assertStringContainsString('同步 1 个区域', $output);
        $this->assertStringContainsString('test-credential-1', $output);
        $this->assertStringContainsString('test-credential-2', $output);
    }

    public function testExecuteWithInvalidCredentialIdShouldShowError(): void
    {
        $commandTester = $this->getCommandTester();

        $exitCode = $commandTester->execute([
            '--credential-id' => '999999',
        ]);

        $this->assertEquals(1, $exitCode);
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('未找到指定的 AWS 凭证', $output);
    }

    public function testOptionCredentialId(): void
    {
        $command = self::getContainer()->get(InstanceSyncCommand::class);
        $this->assertInstanceOf(InstanceSyncCommand::class, $command);

        $definition = $command->getDefinition();
        $this->assertTrue($definition->hasOption('credential-id'));
        $this->assertFalse($definition->getOption('credential-id')->isValueRequired());
    }

    public function testOptionRegion(): void
    {
        $command = self::getContainer()->get(InstanceSyncCommand::class);
        $this->assertInstanceOf(InstanceSyncCommand::class, $command);

        $definition = $command->getDefinition();
        $this->assertTrue($definition->hasOption('region'));
        $this->assertFalse($definition->getOption('region')->isValueRequired());
    }
}
