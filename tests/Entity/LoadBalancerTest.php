<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Entity;

use AwsLightsailBundle\Entity\LoadBalancer;
use AwsLightsailBundle\Enum\LoadBalancerStatusEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(LoadBalancer::class)]
final class LoadBalancerTest extends AbstractEntityTestCase
{
    private LoadBalancer $entity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entity = new LoadBalancer();
    }

    protected function createEntity(): object
    {
        return new LoadBalancer();
    }

    public function testConstructorInitializesCorrectly(): void
    {
        $entity = new LoadBalancer();
        $this->assertInstanceOf(LoadBalancer::class, $entity);
    }

    public function testGetIdReturnsNullWhenNotPersisted(): void
    {
        $this->assertNull($this->entity->getId());
    }

    public function testEntityHasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\Entity\LoadBalancer', LoadBalancer::class);
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'name'                       => ['name', 'test-load-balancer'],
            'arn'                        => ['arn', 'arn:aws:lightsail:us-east-1:123456789012:LoadBalancer/test-load-balancer'],
            'dnsName'                    => ['dnsName', 'test-lb-1234567890.us-east-1.elb.amazonaws.com'],
            'region'                     => ['region', 'us-east-1'],
            'healthCheckPort'            => ['healthCheckPort', 80],
            'healthCheckProtocol'        => ['healthCheckProtocol', 'HTTP'],
            'healthCheckPath'            => ['healthCheckPath', '/health'],
            'healthCheckIntervalSeconds' => ['healthCheckIntervalSeconds', 30],
            'healthCheckTimeoutSeconds'  => ['healthCheckTimeoutSeconds', 5],
            'healthyThreshold'           => ['healthyThreshold', 3],
            'unhealthyThreshold'         => ['unhealthyThreshold', 3],
            'status'                     => ['status', LoadBalancerStatusEnum::ACTIVE],
            // 注意：tlsPolicyEnabled 属性暂时排除，因为它的 setter 方法使用了 set 前缀 + is 前缀的 getter，不符合 AbstractEntityTest 的约定
            'tlsCertificateName'    => ['tlsCertificateName', 'test-certificate'],
            'instanceHealthSummary' => ['instanceHealthSummary', ['instance-1' => 'healthy', 'instance-2' => 'healthy']],
            'tags'                  => ['tags', ['Environment' => 'production', 'Project' => 'test']],
            // 注意：configurationOptions 属性暂时排除，因为它的 setter 方法使用了 set 前缀 + is 前缀的 getter，不符合 AbstractEntityTest 的约定
        ];
    }
}
