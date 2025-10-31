<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Entity;

use AwsLightsailBundle\Entity\Instance;
use AwsLightsailBundle\Enum\InstanceBlueprintEnum;
use AwsLightsailBundle\Enum\InstanceBundleEnum;
use AwsLightsailBundle\Enum\InstanceStateEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Instance::class)]
final class InstanceTest extends AbstractEntityTestCase
{
    private Instance $entity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entity = new Instance();
    }

    protected function createEntity(): object
    {
        return new Instance();
    }

    public function testConstructorInitializesCorrectly(): void
    {
        $entity = new Instance();
        $this->assertInstanceOf(Instance::class, $entity);
    }

    public function testGetIdReturnsNullWhenNotPersisted(): void
    {
        $this->assertNull($this->entity->getId());
    }

    public function testEntityHasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\Entity\Instance', Instance::class);
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'name'             => ['name', 'test-instance'],
            'arn'              => ['arn', 'arn:aws:lightsail:us-east-1:123456789012:Instance/test-instance'],
            'state'            => ['state', InstanceStateEnum::RUNNING],
            'stateCode'        => ['stateCode', 16],
            'blueprint'        => ['blueprint', InstanceBlueprintEnum::WORDPRESS_UBUNTU_20_04],
            'blueprintName'    => ['blueprintName', 'wordpress'],
            'bundle'           => ['bundle', InstanceBundleEnum::NANO_2_0],
            'region'           => ['region', 'us-east-1'],
            'availabilityZone' => ['availabilityZone', 'us-east-1a'],
            'resourceType'     => ['resourceType', 'Instance'],
            'publicIpAddress'  => ['publicIpAddress', '192.168.1.100'],
            'privateIpAddress' => ['privateIpAddress', '172.16.1.100'],
            'ipv6Addresses'    => ['ipv6Addresses', ['2001:db8::1']],
            'ipAddressType'    => ['ipAddressType', 'dualstack'],
            'tags'             => ['tags', ['Environment' => 'test', 'Application' => 'web']],
            'hardware'         => ['hardware', ['cpuCount' => 1, 'ramSizeInGb' => 1]],
            'networking'       => ['networking', ['monthlyTransfer' => ['gbPerMonthAllocated' => 1000]]],
            'metadataOptions'  => ['metadataOptions', ['httpTokens' => 'optional']],
            'awsCreationTime'  => ['awsCreationTime', new \DateTimeImmutable('2023-01-01 12:00:00')],
            'syncTime'         => ['syncTime', new \DateTimeImmutable('2023-01-02 12:00:00')],
            'username'         => ['username', 'admin'],
            'supportCode'      => ['supportCode', 'test-support-code'],
        ];
    }
}
