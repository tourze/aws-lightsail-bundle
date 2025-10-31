<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Entity;

use AwsLightsailBundle\Entity\DiskSnapshot;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(DiskSnapshot::class)]
final class DiskSnapshotTest extends AbstractEntityTestCase
{
    private DiskSnapshot $entity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entity = new DiskSnapshot();
    }

    protected function createEntity(): object
    {
        return new DiskSnapshot();
    }

    public function testConstructorInitializesCorrectly(): void
    {
        $entity = new DiskSnapshot();
        $this->assertInstanceOf(DiskSnapshot::class, $entity);
    }

    public function testGetIdReturnsNullWhenNotPersisted(): void
    {
        $this->assertNull($this->entity->getId());
    }

    public function testEntityHasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\Entity\DiskSnapshot', DiskSnapshot::class);
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'name'     => ['name', 'test-disk-snapshot'],
            'arn'      => ['arn', 'arn:aws:lightsail:us-east-1:123456789012:DiskSnapshot/test-disk-snapshot'],
            'diskName' => ['diskName', 'test-disk'],
            'diskPath' => ['diskPath', '/dev/xvdf'],
            'region'   => ['region', 'us-east-1'],
            'sizeInGb' => ['sizeInGb', 100],
            'state'    => ['state', 'completed'],
            'progress' => ['progress', '100%'],
            'tags'     => ['tags', ['Environment' => 'production', 'Project' => 'test']],
            // 注意：isFromAutoSnapshot 属性暂时排除，因为它的 setter 方法使用了 setIs 前缀，不符合 AbstractEntityTest 的约定
            'fromDiskSnapshotName' => ['fromDiskSnapshotName', 'source-snapshot'],
            'fromRegion'           => ['fromRegion', 'us-west-2'],
            'syncTime'             => ['syncTime', new \DateTimeImmutable('2023-01-01 12:00:00')],
        ];
    }
}
