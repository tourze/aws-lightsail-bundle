<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Command;

use AwsLightsailBundle\Command\InstanceCreateCommand;
use AwsLightsailBundle\Repository\AwsCredentialRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

final class InstanceCreateCommandTest extends TestCase
{
    private CommandTester $commandTester;
    private MockObject&AwsCredentialRepository $credentialRepository;

    protected function setUp(): void
    {
        $this->credentialRepository = $this->createMock(AwsCredentialRepository::class);

        $command = new InstanceCreateCommand($this->credentialRepository);

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

        $this->commandTester->execute([
            'name' => 'test-instance',
        ]);

        $this->assertSame(1, $this->commandTester->getStatusCode());
        $this->assertStringContainsString('未找到任何 AWS 凭证', $this->commandTester->getDisplay());
    }

    public function testGetCommandName(): void
    {
        $this->assertSame('aws:lightsail:instance:create', InstanceCreateCommand::NAME);
    }
}