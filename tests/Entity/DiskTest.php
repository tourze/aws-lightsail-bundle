<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Entity;

use AwsLightsailBundle\Entity\Disk;
use AwsLightsailBundle\Enum\DiskStateEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Disk::class)]
final class DiskTest extends AbstractEntityTestCase
{
    private Disk $entity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entity = new Disk();
    }

    protected function createEntity(): object
    {
        return new Disk();
    }

    public function testConstructorInitializesCorrectly(): void
    {
        $entity = new Disk();
        $this->assertInstanceOf(Disk::class, $entity);
    }

    public function testGetIdReturnsNullWhenNotPersisted(): void
    {
        $this->assertNull($this->entity->getId());
    }

    public function testEntityHasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\Entity\Disk', Disk::class);
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'name'            => ['name', 'test-disk'],
            'arn'             => ['arn', 'arn:aws:lightsail:us-east-1:123456789012:Disk/test-disk'],
            'attachedTo'      => ['attachedTo', 'test-instance'],
            'attachmentState' => ['attachmentState', 'attached'],
            // 注意：isSystemDisk 属性暂时排除，因为它的 setter 方法使用了 setIs 前缀，不符合 AbstractEntityTest 的约定
            'state'    => ['state', DiskStateEnum::AVAILABLE],
            'region'   => ['region', 'us-east-1'],
            'sizeInGb' => ['sizeInGb', 100],
            'iops'     => ['iops', 3000],
            'path'     => ['path', '/dev/xvdf'],
            'tags'     => ['tags', ['Environment' => 'production', 'Project' => 'test']],
            // 注意：isAutoSnapshotConfigured 属性暂时排除，因为它的 setter 方法使用了 setIs 前缀，不符合 AbstractEntityTest 的约定
            'supportCode' => ['supportCode', '123456789012/test-disk/12345678-1234-1234-1234-123456789012'],
            'syncTime'    => ['syncTime', new \DateTimeImmutable('2023-01-01 12:00:00')],
        ];
    }
}
