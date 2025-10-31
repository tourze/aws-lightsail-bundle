<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Repository;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Database;
use AwsLightsailBundle\Entity\DatabaseSnapshot;
use AwsLightsailBundle\Enum\DatabaseEngineEnum;
use AwsLightsailBundle\Repository\DatabaseSnapshotRepository;
use Doctrine\DBAL\Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(DatabaseSnapshotRepository::class)]
#[RunTestsInSeparateProcesses]
final class DatabaseSnapshotRepositoryTest extends AbstractRepositoryTestCase
{
    protected function createNewEntity(): DatabaseSnapshot
    {
        $databaseSnapshot = new DatabaseSnapshot();
        $databaseSnapshot->setName('test-db-snapshot');
        $databaseSnapshot->setArn('arn:aws:lightsail:us-east-1:123456789012:DatabaseSnapshot/test-db-snapshot');
        $databaseSnapshot->setRegion('us-east-1');
        $databaseSnapshot->setDatabaseName('test-database');
        $databaseSnapshot->setEngineVersion('8.0');
        $databaseSnapshot->setCredential($this->createTestAwsCredential());

        return $databaseSnapshot;
    }

    protected function getRepository(): DatabaseSnapshotRepository
    {
        $repository = self::getContainer()->get(DatabaseSnapshotRepository::class);
        $this->assertInstanceOf(DatabaseSnapshotRepository::class, $repository);

        return $repository;
    }

    protected function onSetUp(): void
    {
        // No additional setup needed
    }

    /**
     * 创建测试用的 AwsCredential 实体
     */
    private function createTestAwsCredential(): AwsCredential
    {
        $credential = new AwsCredential();
        $credential->setName('test-credential');
        $credential->setAccessKeyId('test-access-key');
        $credential->setSecretAccessKey('test-secret-key');
        $credential->setRegion('us-east-1');

        return $credential;
    }

    /**
     * 测试查找存在的实体
     */
    public function testFindExistingEntity(): void
    {
        $entity = $this->createNewEntity();
        $this->assertInstanceOf(DatabaseSnapshot::class, $entity);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $foundEntity = $this->getRepository()->find($entity->getId());
        $this->assertNotNull($foundEntity);
        $this->assertInstanceOf(DatabaseSnapshot::class, $foundEntity);
        $this->assertSame($entity->getName(), $foundEntity->getName());
    }

    /**
     * 测试保存和检索实体
     */
    public function testSaveAndRetrieveEntity(): void
    {
        $entity = $this->createNewEntity();
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $foundEntity = $this->getRepository()->findOneBy(['name' => 'test-db-snapshot']);
        $this->assertNotNull($foundEntity);
        $this->assertSame('us-east-1', $foundEntity->getRegion());
    }

    /**
     * 测试数据库异常处理的另一种方式
     * 提供一个额外的测试来确保数据库异常处理正常，避免基类方法的副作用
     */
    public function testDatabaseExceptionHandling(): void
    {
        $connection = self::getEntityManager()->getConnection();

        // 确保连接是活跃的（通过简单查询让Doctrine自己处理连接）
        if (!$connection->isConnected()) {
            try {
                $connection->executeQuery('SELECT 1');
            } catch (\Exception $e) {
                self::markTestSkipped('Database connection not available');
            }
        }

        // 使用无效的SQL查询来触发数据库异常
        $this->expectException(Exception::class);
        $connection->executeQuery('SELECT COUNT(*) FROM nonexistent_table_that_will_cause_error');
    }

    /**
     * 测试按数据库引擎查找快照
     */
    public function testFindByEngine(): void
    {
        $uniqueId = \uniqid('test-engine-', true);

        // 创建 MySQL 快照
        $mysqlSnapshot = $this->createNewEntity();
        $mysqlSnapshot->setName('mysql-snapshot-1-' . $uniqueId);
        $mysqlSnapshot->setEngine(DatabaseEngineEnum::MYSQL);
        $mysqlSnapshot->setDatabaseName('mysql-db-' . $uniqueId);
        self::getEntityManager()->persist($mysqlSnapshot);

        // 创建另一个 MySQL 快照
        $mysqlSnapshot2 = new DatabaseSnapshot();
        $mysqlSnapshot2->setName('mysql-snapshot-2-' . $uniqueId);
        $mysqlSnapshot2->setArn('arn:aws:lightsail:us-east-1:123456789012:DatabaseSnapshot/mysql-snapshot-2-' . $uniqueId);
        $mysqlSnapshot2->setRegion('us-east-1');
        $mysqlSnapshot2->setDatabaseName('mysql-db-2-' . $uniqueId);
        $mysqlSnapshot2->setEngineVersion('8.0');
        $mysqlSnapshot2->setEngine(DatabaseEngineEnum::MYSQL);
        $mysqlSnapshot2->setCredential($this->createTestAwsCredential());
        self::getEntityManager()->persist($mysqlSnapshot2);

        // 创建 PostgreSQL 快照
        $postgresSnapshot = new DatabaseSnapshot();
        $postgresSnapshot->setName('postgres-snapshot-1-' . $uniqueId);
        $postgresSnapshot->setArn('arn:aws:lightsail:us-east-1:123456789012:DatabaseSnapshot/postgres-snapshot-1-' . $uniqueId);
        $postgresSnapshot->setRegion('us-east-1');
        $postgresSnapshot->setDatabaseName('postgres-db-' . $uniqueId);
        $postgresSnapshot->setEngineVersion('14.0');
        $postgresSnapshot->setEngine(DatabaseEngineEnum::POSTGRES);
        $postgresSnapshot->setCredential($this->createTestAwsCredential());
        self::getEntityManager()->persist($postgresSnapshot);

        self::getEntityManager()->flush();

        // 测试查找 MySQL 快照（只验证我们创建的）
        $mysqlSnapshots = $this->getRepository()->findByEngine(DatabaseEngineEnum::MYSQL);
        $this->assertGreaterThanOrEqual(2, \count($mysqlSnapshots));
        $this->assertContainsOnlyInstancesOf(DatabaseSnapshot::class, $mysqlSnapshots);

        // 验证我们创建的快照确实在结果中
        $mysqlNames = \array_map(fn ($s) => $s->getName(), $mysqlSnapshots);
        $this->assertContains('mysql-snapshot-1-' . $uniqueId, $mysqlNames);
        $this->assertContains('mysql-snapshot-2-' . $uniqueId, $mysqlNames);

        // 测试查找 PostgreSQL 快照（只验证我们创建的）
        $postgresSnapshots = $this->getRepository()->findByEngine(DatabaseEngineEnum::POSTGRES);
        $this->assertGreaterThanOrEqual(1, \count($postgresSnapshots));
        $postgresNames = \array_map(fn ($s) => $s->getName(), $postgresSnapshots);
        $this->assertContains('postgres-snapshot-1-' . $uniqueId, $postgresNames);
    }

    /**
     * 测试按区域查找数据库快照
     */
    public function testFindByRegion(): void
    {
        $uniqueId = \uniqid('test-region-', true);

        // 创建 us-east-1 区域快照
        $eastSnapshot1 = $this->createNewEntity();
        $eastSnapshot1->setName('east-snapshot-1-' . $uniqueId);
        $eastSnapshot1->setRegion('us-east-1');
        $eastSnapshot1->setDatabaseName('db-east-1-' . $uniqueId);
        self::getEntityManager()->persist($eastSnapshot1);

        // 创建另一个 us-east-1 区域快照
        $eastSnapshot2 = new DatabaseSnapshot();
        $eastSnapshot2->setName('east-snapshot-2-' . $uniqueId);
        $eastSnapshot2->setArn('arn:aws:lightsail:us-east-1:123456789012:DatabaseSnapshot/east-snapshot-2-' . $uniqueId);
        $eastSnapshot2->setRegion('us-east-1');
        $eastSnapshot2->setDatabaseName('db-east-2-' . $uniqueId);
        $eastSnapshot2->setEngineVersion('8.0');
        $eastSnapshot2->setCredential($this->createTestAwsCredential());
        self::getEntityManager()->persist($eastSnapshot2);

        // 创建 us-west-2 区域快照
        $westSnapshot = new DatabaseSnapshot();
        $westSnapshot->setName('west-snapshot-1-' . $uniqueId);
        $westSnapshot->setArn('arn:aws:lightsail:us-west-2:123456789012:DatabaseSnapshot/west-snapshot-1-' . $uniqueId);
        $westSnapshot->setRegion('us-west-2');
        $westSnapshot->setDatabaseName('db-west-' . $uniqueId);
        $westSnapshot->setEngineVersion('8.0');
        $credential = new AwsCredential();
        $credential->setName('west-credential-' . $uniqueId);
        $credential->setAccessKeyId('test-access-key');
        $credential->setSecretAccessKey('test-secret-key');
        $credential->setRegion('us-west-2');
        $westSnapshot->setCredential($credential);
        self::getEntityManager()->persist($westSnapshot);

        self::getEntityManager()->flush();

        // 测试查找 us-east-1 区域的快照（只验证我们创建的）
        $eastSnapshots = $this->getRepository()->findByRegion('us-east-1');
        $this->assertGreaterThanOrEqual(2, \count($eastSnapshots));
        $this->assertContainsOnlyInstancesOf(DatabaseSnapshot::class, $eastSnapshots);
        $eastNames = \array_map(fn ($s) => $s->getName(), $eastSnapshots);
        $this->assertContains('east-snapshot-1-' . $uniqueId, $eastNames);
        $this->assertContains('east-snapshot-2-' . $uniqueId, $eastNames);

        // 测试查找 us-west-2 区域的快照（只验证我们创建的）
        $westSnapshots = $this->getRepository()->findByRegion('us-west-2');
        $this->assertGreaterThanOrEqual(1, \count($westSnapshots));
        $westNames = \array_map(fn ($s) => $s->getName(), $westSnapshots);
        $this->assertContains('west-snapshot-1-' . $uniqueId, $westNames);

        // 测试查找不存在的区域（使用唯一的区域名）
        $emptyResult = $this->getRepository()->findByRegion('test-nonexistent-' . $uniqueId);
        $this->assertCount(0, $emptyResult);
    }

    /**
     * 测试查找自动快照
     */
    public function testFindAutoSnapshots(): void
    {
        $uniqueId = \uniqid('test-auto-', true);

        // 创建自动快照
        $autoSnapshot1 = $this->createNewEntity();
        $autoSnapshot1->setName('auto-snapshot-1-' . $uniqueId);
        $autoSnapshot1->setIsFromAutoSnapshot(true);
        $autoSnapshot1->setDatabaseName('auto-db-1-' . $uniqueId);
        $autoSnapshot1->setArn('arn:aws:lightsail:us-east-1:123456789012:DatabaseSnapshot/auto-snapshot-1-' . $uniqueId);
        self::getEntityManager()->persist($autoSnapshot1);

        // 创建另一个自动快照
        $autoSnapshot2 = new DatabaseSnapshot();
        $autoSnapshot2->setName('auto-snapshot-2-' . $uniqueId);
        $autoSnapshot2->setArn('arn:aws:lightsail:us-east-1:123456789012:DatabaseSnapshot/auto-snapshot-2-' . $uniqueId);
        $autoSnapshot2->setRegion('us-east-1');
        $autoSnapshot2->setDatabaseName('auto-db-2-' . $uniqueId);
        $autoSnapshot2->setEngineVersion('8.0');
        $autoSnapshot2->setIsFromAutoSnapshot(true);
        $autoSnapshot2->setCredential($this->createTestAwsCredential());
        self::getEntityManager()->persist($autoSnapshot2);

        // 创建手动快照
        $manualSnapshot = new DatabaseSnapshot();
        $manualSnapshot->setName('manual-snapshot-1-' . $uniqueId);
        $manualSnapshot->setArn('arn:aws:lightsail:us-east-1:123456789012:DatabaseSnapshot/manual-snapshot-1-' . $uniqueId);
        $manualSnapshot->setRegion('us-east-1');
        $manualSnapshot->setDatabaseName('manual-db-1-' . $uniqueId);
        $manualSnapshot->setEngineVersion('8.0');
        $manualSnapshot->setIsFromAutoSnapshot(false);
        $manualSnapshot->setCredential($this->createTestAwsCredential());
        self::getEntityManager()->persist($manualSnapshot);

        self::getEntityManager()->flush();

        // 测试查找自动快照
        $autoSnapshots = $this->getRepository()->findAutoSnapshots();
        $this->assertGreaterThanOrEqual(2, \count($autoSnapshots));
        $this->assertContainsOnlyInstancesOf(DatabaseSnapshot::class, $autoSnapshots);

        // 验证我们创建的自动快照在结果中
        $autoNames = \array_map(fn ($s) => $s->getName(), $autoSnapshots);
        $this->assertContains('auto-snapshot-1-' . $uniqueId, $autoNames);
        $this->assertContains('auto-snapshot-2-' . $uniqueId, $autoNames);

        // 验证手动快照不在结果中
        $this->assertNotContains('manual-snapshot-1-' . $uniqueId, $autoNames);

        // 验证所有结果都是自动快照
        foreach ($autoSnapshots as $snapshot) {
            $this->assertTrue($snapshot->isFromAutoSnapshot());
        }
    }

    /**
     * 测试按数据库查找快照
     */
    public function testFindByDatabase(): void
    {
        $uniqueId = \uniqid('test-db-', true);

        // 创建数据库实体
        $database = new Database();
        $database->setName('test-database-' . $uniqueId);
        $database->setArn('arn:aws:lightsail:us-east-1:123456789012:Database/test-database-' . $uniqueId);
        $database->setRegion('us-east-1');
        $database->setEngine(DatabaseEngineEnum::MYSQL);
        $database->setEngineVersion('8.0');
        $database->setMasterUsername('admin');
        $database->setPreferredBackupWindow('00:00-01:00');
        $database->setPreferredMaintenanceWindow('mon:02:00-mon:03:00');
        $database->setBundleId('micro_1_0');
        $database->setCredential($this->createTestAwsCredential());
        self::getEntityManager()->persist($database);

        // 创建关联到该数据库的快照
        $snapshot1 = $this->createNewEntity();
        $snapshot1->setName('db-snapshot-1-' . $uniqueId);
        $snapshot1->setDatabase($database);
        $snapshot1->setDatabaseName('test-database-' . $uniqueId);
        $snapshot1->setArn('arn:aws:lightsail:us-east-1:123456789012:DatabaseSnapshot/db-snapshot-1-' . $uniqueId);
        self::getEntityManager()->persist($snapshot1);

        // 创建另一个关联到该数据库的快照
        $snapshot2 = new DatabaseSnapshot();
        $snapshot2->setName('db-snapshot-2-' . $uniqueId);
        $snapshot2->setArn('arn:aws:lightsail:us-east-1:123456789012:DatabaseSnapshot/db-snapshot-2-' . $uniqueId);
        $snapshot2->setRegion('us-east-1');
        $snapshot2->setDatabaseName('test-database-' . $uniqueId);
        $snapshot2->setEngineVersion('8.0');
        $snapshot2->setDatabase($database);
        $snapshot2->setCredential($this->createTestAwsCredential());
        self::getEntityManager()->persist($snapshot2);

        // 创建不关联该数据库的快照
        $otherSnapshot = new DatabaseSnapshot();
        $otherSnapshot->setName('other-snapshot-1-' . $uniqueId);
        $otherSnapshot->setArn('arn:aws:lightsail:us-east-1:123456789012:DatabaseSnapshot/other-snapshot-1-' . $uniqueId);
        $otherSnapshot->setRegion('us-east-1');
        $otherSnapshot->setDatabaseName('other-database-' . $uniqueId);
        $otherSnapshot->setEngineVersion('8.0');
        $otherSnapshot->setCredential($this->createTestAwsCredential());
        self::getEntityManager()->persist($otherSnapshot);

        self::getEntityManager()->flush();

        // 测试按数据库查找快照
        $results = $this->getRepository()->findByDatabase($database);
        $this->assertGreaterThanOrEqual(2, \count($results));
        $this->assertContainsOnlyInstancesOf(DatabaseSnapshot::class, $results);

        // 验证我们创建的快照在结果中
        $names = \array_map(fn ($s) => $s->getName(), $results);
        $this->assertContains('db-snapshot-1-' . $uniqueId, $names);
        $this->assertContains('db-snapshot-2-' . $uniqueId, $names);

        // 验证不关联的快照不在结果中
        $this->assertNotContains('other-snapshot-1-' . $uniqueId, $names);

        // 验证所有结果都关联到该数据库
        foreach ($results as $snapshot) {
            if (null !== $snapshot->getDatabase()) {
                $this->assertSame($database->getId(), $snapshot->getDatabase()->getId());
            }
        }
    }

    /**
     * 测试按数据库名称查找快照
     */
    public function testFindByDatabaseName(): void
    {
        $uniqueId     = \uniqid('test-dbname-', true);
        $databaseName = 'test-db-name-' . $uniqueId;

        // 创建指定数据库名称的快照
        $snapshot1 = $this->createNewEntity();
        $snapshot1->setName('snapshot-1-' . $uniqueId);
        $snapshot1->setDatabaseName($databaseName);
        $snapshot1->setArn('arn:aws:lightsail:us-east-1:123456789012:DatabaseSnapshot/snapshot-1-' . $uniqueId);
        self::getEntityManager()->persist($snapshot1);

        // 创建另一个指定数据库名称的快照
        $snapshot2 = new DatabaseSnapshot();
        $snapshot2->setName('snapshot-2-' . $uniqueId);
        $snapshot2->setArn('arn:aws:lightsail:us-east-1:123456789012:DatabaseSnapshot/snapshot-2-' . $uniqueId);
        $snapshot2->setRegion('us-east-1');
        $snapshot2->setDatabaseName($databaseName);
        $snapshot2->setEngineVersion('8.0');
        $snapshot2->setCredential($this->createTestAwsCredential());
        self::getEntityManager()->persist($snapshot2);

        // 创建不同数据库名称的快照
        $otherSnapshot = new DatabaseSnapshot();
        $otherSnapshot->setName('other-snapshot-1-' . $uniqueId);
        $otherSnapshot->setArn('arn:aws:lightsail:us-east-1:123456789012:DatabaseSnapshot/other-snapshot-1-' . $uniqueId);
        $otherSnapshot->setRegion('us-east-1');
        $otherSnapshot->setDatabaseName('other-db-name-' . $uniqueId);
        $otherSnapshot->setEngineVersion('8.0');
        $otherSnapshot->setCredential($this->createTestAwsCredential());
        self::getEntityManager()->persist($otherSnapshot);

        self::getEntityManager()->flush();

        // 测试按数据库名称查找快照
        $results = $this->getRepository()->findByDatabaseName($databaseName);
        $this->assertGreaterThanOrEqual(2, \count($results));
        $this->assertContainsOnlyInstancesOf(DatabaseSnapshot::class, $results);

        // 验证我们创建的快照在结果中
        $names = \array_map(fn ($s) => $s->getName(), $results);
        $this->assertContains('snapshot-1-' . $uniqueId, $names);
        $this->assertContains('snapshot-2-' . $uniqueId, $names);

        // 验证不同数据库名称的快照不在结果中
        $this->assertNotContains('other-snapshot-1-' . $uniqueId, $names);

        // 验证所有结果都有正确的数据库名称
        foreach ($results as $snapshot) {
            $this->assertSame($databaseName, $snapshot->getDatabaseName());
        }

        // 测试查找不存在的数据库名称
        $emptyResult = $this->getRepository()->findByDatabaseName('nonexistent-db-' . $uniqueId);
        $this->assertCount(0, $emptyResult);
    }
}
