<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Repository;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Snapshot;
use AwsLightsailBundle\Enum\SnapshotTypeEnum;
use AwsLightsailBundle\Repository\SnapshotRepository;
use Doctrine\DBAL\Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(SnapshotRepository::class)]
#[RunTestsInSeparateProcesses]
final class SnapshotRepositoryTest extends AbstractRepositoryTestCase
{
    protected function createNewEntity(): Snapshot
    {
        $snapshot = new Snapshot();
        $snapshot->setName('test-snapshot');
        $snapshot->setArn('arn:aws:lightsail:us-east-1:123456789012:Snapshot/test-snapshot');
        $snapshot->setRegion('us-east-1');
        $snapshot->setResourceName('test-instance');
        $snapshot->setSizeInGb(32);
        $snapshot->setCredential($this->createTestAwsCredential());

        return $snapshot;
    }

    protected function getRepository(): SnapshotRepository
    {
        $repository = self::getContainer()->get(SnapshotRepository::class);
        $this->assertInstanceOf(SnapshotRepository::class, $repository);

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
        $this->assertInstanceOf(Snapshot::class, $entity);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $foundEntity = $this->getRepository()->find($entity->getId());
        $this->assertNotNull($foundEntity);
        $this->assertInstanceOf(Snapshot::class, $foundEntity);
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

        $foundEntity = $this->getRepository()->findOneBy(['name' => 'test-snapshot']);
        $this->assertNotNull($foundEntity);
        $this->assertSame('us-east-1', $foundEntity->getRegion());
    }

    /**
     * 测试按类型查找快照
     */
    public function testFindByType(): void
    {
        $entity = $this->createNewEntity();
        $entity->setType(SnapshotTypeEnum::INSTANCE);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByType(SnapshotTypeEnum::INSTANCE);

        $this->assertGreaterThanOrEqual(1, \count($result));
        $this->assertInstanceOf(Snapshot::class, $result[0]);
        $this->assertSame(SnapshotTypeEnum::INSTANCE, $result[0]->getType());
    }

    /**
     * 测试按资源名称查找快照
     */
    public function testFindByResourceName(): void
    {
        $entity = $this->createNewEntity();
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByResourceName('test-instance');

        $this->assertGreaterThanOrEqual(1, \count($result));
        $this->assertInstanceOf(Snapshot::class, $result[0]);
        $this->assertSame('test-instance', $result[0]->getResourceName());
    }

    /**
     * 测试按区域查找快照
     */
    public function testFindByRegion(): void
    {
        $entity = $this->createNewEntity();
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByRegion('us-east-1');

        $this->assertGreaterThanOrEqual(1, \count($result));
        $this->assertInstanceOf(Snapshot::class, $result[0]);
        $this->assertSame('us-east-1', $result[0]->getRegion());
    }

    /**
     * 测试查找自动快照
     */
    public function testFindAutoSnapshots(): void
    {
        $entity = $this->createNewEntity();
        $entity->setIsFromAutoSnapshot(true);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findAutoSnapshots();

        $this->assertGreaterThanOrEqual(1, \count($result));
        $this->assertInstanceOf(Snapshot::class, $result[0]);
        $this->assertTrue($result[0]->isFromAutoSnapshot());
    }

    /**
     * 测试按日期范围查找快照
     */
    public function testFindByDateRange(): void
    {
        $entity = $this->createNewEntity();
        $entity->setCreateTime(new \DateTimeImmutable('2023-01-15 10:00:00'));
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $fromDate = new \DateTimeImmutable('2023-01-01');
        $toDate   = new \DateTimeImmutable('2023-01-31');
        $result   = $this->getRepository()->findByDateRange($fromDate, $toDate);

        $this->assertGreaterThanOrEqual(1, \count($result));
        $this->assertInstanceOf(Snapshot::class, $result[0]);
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
}
