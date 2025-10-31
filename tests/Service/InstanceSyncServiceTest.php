<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Service;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Service\InstanceSyncService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(InstanceSyncService::class)]
#[RunTestsInSeparateProcesses]
final class InstanceSyncServiceTest extends AbstractIntegrationTestCase
{
    private InstanceSyncService $service;

    protected function onSetUp(): void
    {
        $this->service = self::getService(InstanceSyncService::class);
    }

    public function testServiceIsInstantiated(): void
    {
        $this->assertInstanceOf(InstanceSyncService::class, $this->service);
    }

    public function testBatchSyncInstancesWithEmptyData(): void
    {
        $credential = new AwsCredential();
        $credential->setName('test-credential');
        $credential->setAccessKeyId('test-access-key');
        $credential->setSecretAccessKey('test-secret-key');

        // Note: Since we're using AbstractIntegrationTestCase, no need to mock flush()

        $result = $this->service->batchSyncInstances($credential, []);

        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('new', $result);
        $this->assertArrayHasKey('updated', $result);
        $this->assertArrayHasKey('errors', $result);
    }

    public function testCleanupDeletedInstances(): void
    {
        $credential = new AwsCredential();
        $credential->setName('test-credential');
        $credential->setAccessKeyId('test-access-key');
        $credential->setSecretAccessKey('test-secret-key');

        $result = $this->service->cleanupDeletedInstances($credential, []);
        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(0, $result);
    }

    public function testUpdateInstanceFromData(): void
    {
        $credential = new AwsCredential();
        $credential->setName('test-credential');
        $credential->setAccessKeyId('test-access-key');
        $credential->setSecretAccessKey('test-secret-key');

        $data = [
            'name'         => 'test-instance',
            'arn'          => 'arn:aws:lightsail:us-east-1:123456789012:Instance/test-instance',
            'bundleId'     => 'nano_2_0',
            'blueprintId'  => 'ubuntu_20_04',
            'state'        => ['name' => 'running'],
            'location'     => ['availabilityZone' => 'us-east-1a', 'regionName' => 'us-east-1'],
            'resourceType' => 'Instance',
        ];

        $instance = $this->service->updateInstanceFromData($credential, $data);

        $this->assertInstanceOf('AwsLightsailBundle\Entity\Instance', $instance);
        $this->assertSame('test-instance', $instance->getName());
    }
}
