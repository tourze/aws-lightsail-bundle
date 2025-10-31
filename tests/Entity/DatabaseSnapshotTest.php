<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Entity;

use AwsLightsailBundle\Entity\DatabaseSnapshot;
use AwsLightsailBundle\Enum\DatabaseEngineEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(DatabaseSnapshot::class)]
final class DatabaseSnapshotTest extends AbstractEntityTestCase
{
    private DatabaseSnapshot $entity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entity = new DatabaseSnapshot();
    }

    protected function createEntity(): object
    {
        return new DatabaseSnapshot();
    }

    public function testConstructorInitializesCorrectly(): void
    {
        $entity = new DatabaseSnapshot();
        $this->assertInstanceOf(DatabaseSnapshot::class, $entity);
    }

    public function testGetIdReturnsNullWhenNotPersisted(): void
    {
        $this->assertNull($this->entity->getId());
    }

    public function testEntityHasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\Entity\DatabaseSnapshot', DatabaseSnapshot::class);
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'name'          => ['name', 'test-db-snapshot'],
            'arn'           => ['arn', 'arn:aws:lightsail:us-east-1:123456789012:DatabaseSnapshot/test-db-snapshot'],
            'databaseName'  => ['databaseName', 'test-database'],
            'engine'        => ['engine', DatabaseEngineEnum::MYSQL],
            'engineVersion' => ['engineVersion', '8.0.28'],
            'sizeInGb'      => ['sizeInGb', 100],
            'region'        => ['region', 'us-east-1'],
            'state'         => ['state', 'available'],
            // 注意：isFromAutoSnapshot 属性暂时排除，因为它的 setter 方法使用了 setIs 前缀，不符合 AbstractEntityTest 的约定
            'tags'     => ['tags', ['Environment' => 'production', 'Project' => 'test']],
            'syncTime' => ['syncTime', new \DateTimeImmutable('2023-01-01 12:00:00')],
        ];
    }
}
