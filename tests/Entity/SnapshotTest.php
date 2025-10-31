<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Entity;

use AwsLightsailBundle\Entity\Snapshot;
use AwsLightsailBundle\Enum\SnapshotTypeEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Snapshot::class)]
final class SnapshotTest extends AbstractEntityTestCase
{
    private Snapshot $entity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entity = new Snapshot();
    }

    protected function createEntity(): object
    {
        return new Snapshot();
    }

    public function testConstructorInitializesCorrectly(): void
    {
        $entity = new Snapshot();
        $this->assertInstanceOf(Snapshot::class, $entity);
    }

    public function testGetIdReturnsNullWhenNotPersisted(): void
    {
        $this->assertNull($this->entity->getId());
    }

    public function testEntityHasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\Entity\Snapshot', Snapshot::class);
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'name'             => ['name', 'test-snapshot'],
            'arn'              => ['arn', 'arn:aws:lightsail:us-east-1:123456789012:Snapshot/test-snapshot'],
            'resourceName'     => ['resourceName', 'test-instance'],
            'type'             => ['type', SnapshotTypeEnum::INSTANCE],
            'region'           => ['region', 'us-east-1'],
            'tags'             => ['tags', ['Environment' => 'production', 'Project' => 'test']],
            'syncTime'         => ['syncTime', new \DateTimeImmutable('2023-01-01 12:00:00')],
            'fromSnapshotName' => ['fromSnapshotName', 'source-snapshot'],
            'fromRegion'       => ['fromRegion', 'us-west-2'],
            'sizeInGb'         => ['sizeInGb', 100],
            'state'            => ['state', 'completed'],
            'progress'         => ['progress', '100%'],
            // 注意：isFromAutoSnapshot 属性暂时排除，因为它的 setter 方法使用了 setIs 前缀，不符合 AbstractEntityTest 的约定
        ];
    }
}
