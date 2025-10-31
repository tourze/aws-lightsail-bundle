<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Entity;

use AwsLightsailBundle\Entity\Database;
use AwsLightsailBundle\Enum\DatabaseEngineEnum;
use AwsLightsailBundle\Enum\DatabaseStatusEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Database::class)]
final class DatabaseTest extends AbstractEntityTestCase
{
    private Database $entity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entity = new Database();
    }

    protected function createEntity(): object
    {
        return new Database();
    }

    public function testConstructorInitializesCorrectly(): void
    {
        $entity = new Database();
        $this->assertInstanceOf(Database::class, $entity);
    }

    public function testGetIdReturnsNullWhenNotPersisted(): void
    {
        $this->assertNull($this->entity->getId());
    }

    public function testEntityHasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\Entity\Database', Database::class);
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'name'                       => ['name', 'test-database'],
            'arn'                        => ['arn', 'arn:aws:lightsail:us-east-1:123456789012:RelationalDatabase/test-database'],
            'engine'                     => ['engine', DatabaseEngineEnum::MYSQL],
            'engineVersion'              => ['engineVersion', '8.0.28'],
            'masterUsername'             => ['masterUsername', 'admin'],
            'masterEndpoint'             => ['masterEndpoint', 'test-database.123456789012.us-east-1.rds.amazonaws.com'],
            'masterPort'                 => ['masterPort', 3306],
            'secondaryEndpoint'          => ['secondaryEndpoint', 'test-database-secondary.123456789012.us-east-1.rds.amazonaws.com'],
            'preferredBackupWindow'      => ['preferredBackupWindow', '16:00-16:30'],
            'preferredMaintenanceWindow' => ['preferredMaintenanceWindow', 'sun:07:00-sun:08:00'],
            // 注意：publiclyAccessible 属性暂时排除，因为它的 setter 方法使用了 set 前缀 + is 前缀的 getter，不符合 AbstractEntityTest 的约定
            'status' => ['status', DatabaseStatusEnum::AVAILABLE],
            'region' => ['region', 'us-east-1'],
            // 注意：supportCode 属性暂时排除，因为它的 setter 方法使用了 set 前缀 + is 前缀的 getter，不符合 AbstractEntityTest 的约定
            'caCertificateIdentifier' => ['caCertificateIdentifier', 'rds-ca-2019'],
            'pendingModifiedValues'   => ['pendingModifiedValues', ['engineVersion' => '8.0.30']],
            // 注意：backupRetentionEnabled 属性暂时排除，因为它的 setter 方法使用了 setIs 前缀，不符合 AbstractEntityTest 的约定
            'tags'     => ['tags', ['Environment' => 'production', 'Project' => 'test']],
            'bundleId' => ['bundleId', 'db-t2.micro'],
            // 注意：autoMinorVersionUpgrade 属性暂时排除，因为它的 setter 方法使用了 setIs 前缀，不符合 AbstractEntityTest 的约定
            'syncTime' => ['syncTime', new \DateTimeImmutable('2023-01-01 12:00:00')],
        ];
    }
}
