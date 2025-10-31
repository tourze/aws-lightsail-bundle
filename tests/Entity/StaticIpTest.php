<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Entity;

use AwsLightsailBundle\Entity\StaticIp;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(StaticIp::class)]
final class StaticIpTest extends AbstractEntityTestCase
{
    private StaticIp $entity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entity = new StaticIp();
    }

    protected function createEntity(): object
    {
        return new StaticIp();
    }

    public function testConstructorInitializesCorrectly(): void
    {
        $entity = new StaticIp();
        $this->assertInstanceOf(StaticIp::class, $entity);
    }

    public function testGetIdReturnsNullWhenNotPersisted(): void
    {
        $this->assertNull($this->entity->getId());
    }

    public function testEntityHasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\Entity\StaticIp', StaticIp::class);
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'name'       => ['name', 'test-static-ip'],
            'arn'        => ['arn', 'arn:aws:lightsail:us-east-1:123456789012:StaticIp/test-static-ip'],
            'ipAddress'  => ['ipAddress', '192.168.1.100'],
            'attachedTo' => ['attachedTo', 'test-instance'],
            // 注意：isAttached 属性暂时排除，因为它的 setter 方法使用了 setIs 前缀，不符合 AbstractEntityTest 的约定
            'region'   => ['region', 'us-east-1'],
            'syncTime' => ['syncTime', new \DateTimeImmutable('2023-01-01 12:00:00')],
        ];
    }
}
