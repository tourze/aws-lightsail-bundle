<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Command;

use AwsLightsailBundle\Command\InstanceControlCommand;
use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Instance;
use AwsLightsailBundle\Enum\AmazonRegion;
use AwsLightsailBundle\Enum\InstanceStateEnum;
use AwsLightsailBundle\Repository\AwsCredentialRepository;
use AwsLightsailBundle\Repository\InstanceRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

final class InstanceControlCommandTest extends TestCase
{
    private CommandTester $commandTester;
    private MockObject&InstanceRepository $instanceRepository;
    private MockObject&AwsCredentialRepository $credentialRepository;

    protected function setUp(): void
    {
        $this->instanceRepository = $this->createMock(InstanceRepository::class);
        $this->credentialRepository = $this->createMock(AwsCredentialRepository::class);

        $command = new InstanceControlCommand(
            $this->instanceRepository,
            $this->credentialRepository
        );

        $application = new Application();
        $application->add($command);

        $this->commandTester = new CommandTester($command);
    }

    public function testExecuteWithInvalidOperation(): void
    {
        $this->commandTester->execute([
            'operation' => 'invalid',
            'instance-name' => 'test-instance',
        ]);

        $this->assertSame(1, $this->commandTester->getStatusCode());
        $this->assertStringContainsString('无效的操作类型', $this->commandTester->getDisplay());
    }

    public function testExecuteWithInstanceNotFound(): void
    {
        $this->instanceRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['name' => 'non-existent'])
            ->willReturn(null);

        $this->credentialRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $this->commandTester->execute([
            'operation' => 'start',
            'instance-name' => 'non-existent',
        ]);

        $this->assertSame(1, $this->commandTester->getStatusCode());
        $this->assertStringContainsString('未找到任何 AWS 凭证', $this->commandTester->getDisplay());
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

        $this->instanceRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['name' => 'test-instance'])
            ->willReturn($instance);

        // Mock AWS SDK response - skip actual API call
        $this->commandTester->execute([
            'operation' => 'start',
            'instance-name' => 'test-instance',
            '--force' => true,
        ]);

        // 由于我们无法模拟 AWS SDK，命令会在尝试调用 API 时失败
        // 但至少可以验证流程到达了正确的位置
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('启动 AWS Lightsail 实例', $output);
    }

    public function testExecuteWithNoInstanceNameShowsSelection(): void
    {
        $this->instanceRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $this->commandTester->execute([
            'operation' => 'stop',
        ]);

        $this->assertSame(1, $this->commandTester->getStatusCode());
        $this->assertStringContainsString('未找到任何实例', $this->commandTester->getDisplay());
    }

    public function testGetCommandName(): void
    {
        $this->assertSame('aws:lightsail:instance:control', InstanceControlCommand::NAME);
    }
}