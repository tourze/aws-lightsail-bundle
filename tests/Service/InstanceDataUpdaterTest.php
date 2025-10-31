<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Service;

use AwsLightsailBundle\Entity\Instance;
use AwsLightsailBundle\Service\InstanceDataUpdater;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(InstanceDataUpdater::class)]
#[RunTestsInSeparateProcesses]
final class InstanceDataUpdaterTest extends AbstractIntegrationTestCase
{
    private InstanceDataUpdater $service;

    protected function onSetUp(): void
    {
        $this->service = self::getService(InstanceDataUpdater::class);
    }

    public function testUpdateHardwareAndConfigFields(): void
    {
        $instance = new Instance();
        $data     = [
            'hardware'        => ['cpu' => 2, 'memory' => 4],
            'metadataOptions' => ['http_endpoint' => 'enabled'],
            'tags'            => [
                ['key' => 'Environment', 'value' => 'production'],
                ['key' => 'Team', 'value' => 'backend'],
            ],
            'isMonitored' => true,
        ];

        $this->service->updateHardwareAndConfigFields($instance, $data);

        $this->assertSame(['cpu' => 2, 'memory' => 4], $instance->getHardware());
        $this->assertSame(['http_endpoint' => 'enabled'], $instance->getMetadataOptions());
        $this->assertSame(['Environment' => 'production', 'Team' => 'backend'], $instance->getTags());
        $this->assertTrue($instance->isMonitoring());
    }

    public function testUpdateHardwareAndConfigFieldsWithPartialData(): void
    {
        $instance = new Instance();
        $data     = [
            'hardware' => ['cpu' => 1],
        ];

        $this->service->updateHardwareAndConfigFields($instance, $data);

        $this->assertSame(['cpu' => 1], $instance->getHardware());
        $this->assertNull($instance->getMetadataOptions());
        $this->assertNull($instance->getTags());
        $this->assertFalse($instance->isMonitoring());
    }

    public function testUpdateTimestampFieldsWithValidDateTime(): void
    {
        $instance  = new Instance();
        $createdAt = new \DateTimeImmutable('2024-01-01 12:00:00');
        $data      = ['createdAt' => $createdAt];

        $this->service->updateTimestampFields($instance, $data);

        $this->assertEquals($createdAt, $instance->getAwsCreationTime());
    }

    public function testUpdateTimestampFieldsWithStringDateTime(): void
    {
        $instance = new Instance();
        $data     = ['createdAt' => '2024-01-01T12:00:00Z'];

        $this->service->updateTimestampFields($instance, $data);

        $this->assertNotNull($instance->getAwsCreationTime());
        $this->assertSame('2024-01-01', $instance->getAwsCreationTime()->format('Y-m-d'));
    }

    public function testUpdateTimestampFieldsWithInvalidDateTime(): void
    {
        $instance = new Instance();
        $data     = ['createdAt' => 'invalid-date'];

        $this->service->updateTimestampFields($instance, $data);

        $this->assertNull($instance->getAwsCreationTime());
    }

    public function testUpdateTimestampFieldsWithoutCreatedAt(): void
    {
        $instance = new Instance();
        $data     = [];

        $this->service->updateTimestampFields($instance, $data);

        $this->assertNull($instance->getAwsCreationTime());
    }

    public function testUpdateNetworkFields(): void
    {
        $instance = new Instance();
        $data     = [
            'publicIpAddress'  => '203.0.113.1',
            'privateIpAddress' => '10.0.1.100',
            'ipv6Addresses'    => ['2001:db8::1'],
            'ipAddressType'    => 'ipv4',
            'isStaticIp'       => true,
            'networking'       => ['ports' => ['22', '80', '443']],
        ];

        $this->service->updateNetworkFields($instance, $data);

        $this->assertSame('203.0.113.1', $instance->getPublicIpAddress());
        $this->assertSame('10.0.1.100', $instance->getPrivateIpAddress());
        $this->assertSame(['2001:db8::1'], $instance->getIpv6Addresses());
        $this->assertSame('ipv4', $instance->getIpAddressType());
        $this->assertTrue($instance->isStaticIp());
        $this->assertSame(['ports' => ['22', '80', '443']], $instance->getNetworking());
    }

    public function testUpdateNetworkFieldsWithPartialData(): void
    {
        $instance = new Instance();
        $data     = [
            'publicIpAddress' => '203.0.113.1',
        ];

        $this->service->updateNetworkFields($instance, $data);

        $this->assertSame('203.0.113.1', $instance->getPublicIpAddress());
        $this->assertNull($instance->getPrivateIpAddress());
        $this->assertNull($instance->getIpv6Addresses());
        $this->assertNull($instance->getIpAddressType());
        $this->assertFalse($instance->isStaticIp());
        $this->assertNull($instance->getNetworking());
    }

    public function testServiceIsInstantiated(): void
    {
        $this->assertInstanceOf(InstanceDataUpdater::class, $this->service);
    }
}
