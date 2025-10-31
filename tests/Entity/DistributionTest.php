<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Entity;

use AwsLightsailBundle\Entity\Distribution;
use AwsLightsailBundle\Enum\DistributionStatusEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Distribution::class)]
final class DistributionTest extends AbstractEntityTestCase
{
    private Distribution $entity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entity = new Distribution();
    }

    protected function createEntity(): object
    {
        return new Distribution();
    }

    public function testConstructorInitializesCorrectly(): void
    {
        $entity = new Distribution();
        $this->assertInstanceOf(Distribution::class, $entity);
    }

    public function testGetIdReturnsNullWhenNotPersisted(): void
    {
        $this->assertNull($this->entity->getId());
    }

    public function testEntityHasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\Entity\Distribution', Distribution::class);
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'name'                 => ['name', 'test-distribution'],
            'arn'                  => ['arn', 'arn:aws:lightsail:us-east-1:123456789012:Distribution/test-distribution'],
            'defaultDomainName'    => ['defaultDomainName', 'example.cloudfront.net'],
            'status'               => ['status', DistributionStatusEnum::ACTIVE],
            'region'               => ['region', 'us-east-1'],
            'originConfigs'        => ['originConfigs', ['originType' => 'loadBalancer', 'originPath' => '/']],
            'defaultCacheBehavior' => ['defaultCacheBehavior', ['targetOriginId' => 'origin1', 'viewerProtocolPolicy' => 'allow-all']],
            'cacheBehaviors'       => ['cacheBehaviors', [['pathPattern' => '/images/*', 'targetOriginId' => 'origin1']]],
            // 注意：isEnabled 属性暂时排除，因为它的 setter 方法使用了 setIs 前缀，不符合 AbstractEntityTest 的约定
            'certificateName'        => ['certificateName', 'test-certificate'],
            'viewerProtocolPolicy'   => ['viewerProtocolPolicy', true],
            'tags'                   => ['tags', ['Environment' => 'production', 'Project' => 'test']],
            'syncTime'               => ['syncTime', new \DateTimeImmutable('2023-01-01 12:00:00')],
            'alternativeDomainNames' => ['alternativeDomainNames', ['cdn.example.com', 'static.example.com']],
            'originPublicDNS'        => ['originPublicDNS', ['origin1.example.com', 'origin2.example.com']],
        ];
    }
}
