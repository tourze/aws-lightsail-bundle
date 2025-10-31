<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Repository;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Disk;
use AwsLightsailBundle\Entity\DiskSnapshot;
use AwsLightsailBundle\Repository\DiskSnapshotRepository;
use Doctrine\DBAL\Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(DiskSnapshotRepository::class)]
#[RunTestsInSeparateProcesses]
final class DiskSnapshotRepositoryTest extends AbstractRepositoryTestCase
{
    protected function createNewEntity(): DiskSnapshot
    {
        $diskSnapshot = new DiskSnapshot();
        $diskSnapshot->setName('test-disk-snapshot');
        $diskSnapshot->setArn('arn:aws:lightsail:us-east-1:123456789012:DiskSnapshot/test-disk-snapshot');
        $diskSnapshot->setRegion('us-east-1');
        $diskSnapshot->setDiskName('test-disk');
        $diskSnapshot->setSizeInGb(32);
        $diskSnapshot->setCredential($this->createTestAwsCredential());

        return $diskSnapshot;
    }

    protected function getRepository(): DiskSnapshotRepository
    {
        $repository = self::getContainer()->get(DiskSnapshotRepository::class);
        $this->assertInstanceOf(DiskSnapshotRepository::class, $repository);

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
        $this->assertInstanceOf(DiskSnapshot::class, $entity);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $foundEntity = $this->getRepository()->find($entity->getId());
        $this->assertNotNull($foundEntity);
        $this->assertInstanceOf(DiskSnapshot::class, $foundEntity);
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

        $foundEntity = $this->getRepository()->findOneBy(['name' => 'test-disk-snapshot']);
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

        // 确保连接当前是活跃的
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
     * 测试查找自动创建的快照
     */
    public function testFindAutoSnapshots(): void
    {
        // 创建自动快照
        $autoSnapshot1 = $this->createNewEntity();
        $autoSnapshot1->setName('auto-snapshot-1');
        $autoSnapshot1->setIsFromAutoSnapshot(true);
        self::getEntityManager()->persist($autoSnapshot1);

        // 创建另一个自动快照
        $autoSnapshot2 = new DiskSnapshot();
        $autoSnapshot2->setName('auto-snapshot-2');
        $autoSnapshot2->setArn('arn:aws:lightsail:us-east-1:123456789012:DiskSnapshot/auto-snapshot-2');
        $autoSnapshot2->setRegion('us-east-1');
        $autoSnapshot2->setDiskName('test-disk-2');
        $autoSnapshot2->setSizeInGb(64);
        $autoSnapshot2->setIsFromAutoSnapshot(true);
        $autoSnapshot2->setCredential($this->createTestAwsCredential());
        self::getEntityManager()->persist($autoSnapshot2);

        // 创建手动快照
        $manualSnapshot = new DiskSnapshot();
        $manualSnapshot->setName('manual-snapshot-1');
        $manualSnapshot->setArn('arn:aws:lightsail:us-east-1:123456789012:DiskSnapshot/manual-snapshot-1');
        $manualSnapshot->setRegion('us-east-1');
        $manualSnapshot->setDiskName('test-disk-3');
        $manualSnapshot->setSizeInGb(32);
        $manualSnapshot->setIsFromAutoSnapshot(false);
        $manualSnapshot->setCredential($this->createTestAwsCredential());
        self::getEntityManager()->persist($manualSnapshot);

        self::getEntityManager()->flush();

        // 测试查找自动快照
        $autoSnapshots = $this->getRepository()->findAutoSnapshots();
        $this->assertCount(2, $autoSnapshots);
        $this->assertContainsOnlyInstancesOf(DiskSnapshot::class, $autoSnapshots);
        foreach ($autoSnapshots as $snapshot) {
            $this->assertTrue($snapshot->isFromAutoSnapshot());
        }
    }

    /**
     * 测试按磁盘查找快照
     */
    public function testFindByDisk(): void
    {
        // 创建测试磁盘
        $disk = new Disk();
        $disk->setName('test-disk-for-snapshot');
        $disk->setArn('arn:aws:lightsail:us-east-1:123456789012:Disk/test-disk-for-snapshot');
        $disk->setRegion('us-east-1');
        $disk->setSizeInGb(32);
        $disk->setCredential($this->createTestAwsCredential());
        self::getEntityManager()->persist($disk);

        // 创建关联到该磁盘的快照
        $snapshot1 = $this->createNewEntity();
        $snapshot1->setName('disk-snapshot-1');
        $snapshot1->setDisk($disk);
        self::getEntityManager()->persist($snapshot1);

        // 创建另一个关联到该磁盘的快照
        $snapshot2 = new DiskSnapshot();
        $snapshot2->setName('disk-snapshot-2');
        $snapshot2->setArn('arn:aws:lightsail:us-east-1:123456789012:DiskSnapshot/disk-snapshot-2');
        $snapshot2->setRegion('us-east-1');
        $snapshot2->setDiskName('test-disk-for-snapshot');
        $snapshot2->setSizeInGb(32);
        $snapshot2->setDisk($disk);
        $snapshot2->setCredential($this->createTestAwsCredential());
        self::getEntityManager()->persist($snapshot2);

        // 创建不关联磁盘的快照
        $snapshot3 = new DiskSnapshot();
        $snapshot3->setName('disk-snapshot-3');
        $snapshot3->setArn('arn:aws:lightsail:us-east-1:123456789012:DiskSnapshot/disk-snapshot-3');
        $snapshot3->setRegion('us-east-1');
        $snapshot3->setDiskName('other-disk');
        $snapshot3->setSizeInGb(32);
        $snapshot3->setCredential($this->createTestAwsCredential());
        self::getEntityManager()->persist($snapshot3);

        self::getEntityManager()->flush();

        // 测试按磁盘查找快照
        $diskSnapshots = $this->getRepository()->findByDisk($disk);
        $this->assertCount(2, $diskSnapshots);
        $this->assertContainsOnlyInstancesOf(DiskSnapshot::class, $diskSnapshots);
    }

    /**
     * 测试按磁盘名称查找快照
     */
    public function testFindByDiskName(): void
    {
        // 创建指定磁盘名称的快照
        $snapshot1 = $this->createNewEntity();
        $snapshot1->setName('snapshot-1');
        $snapshot1->setDiskName('my-disk');
        self::getEntityManager()->persist($snapshot1);

        // 创建另一个同磁盘名称的快照
        $snapshot2 = new DiskSnapshot();
        $snapshot2->setName('snapshot-2');
        $snapshot2->setArn('arn:aws:lightsail:us-east-1:123456789012:DiskSnapshot/snapshot-2');
        $snapshot2->setRegion('us-east-1');
        $snapshot2->setDiskName('my-disk');
        $snapshot2->setSizeInGb(64);
        $snapshot2->setCredential($this->createTestAwsCredential());
        self::getEntityManager()->persist($snapshot2);

        // 创建不同磁盘名称的快照
        $snapshot3 = new DiskSnapshot();
        $snapshot3->setName('snapshot-3');
        $snapshot3->setArn('arn:aws:lightsail:us-east-1:123456789012:DiskSnapshot/snapshot-3');
        $snapshot3->setRegion('us-east-1');
        $snapshot3->setDiskName('other-disk');
        $snapshot3->setSizeInGb(32);
        $snapshot3->setCredential($this->createTestAwsCredential());
        self::getEntityManager()->persist($snapshot3);

        self::getEntityManager()->flush();

        // 测试按磁盘名称查找
        $myDiskSnapshots = $this->getRepository()->findByDiskName('my-disk');
        $this->assertCount(2, $myDiskSnapshots);
        foreach ($myDiskSnapshots as $snapshot) {
            $this->assertSame('my-disk', $snapshot->getDiskName());
        }

        // 测试查找不存在的磁盘名称
        $emptyResult = $this->getRepository()->findByDiskName('nonexistent-disk');
        $this->assertCount(0, $emptyResult);
    }

    /**
     * 测试按区域查找磁盘快照
     */
    public function testFindByRegion(): void
    {
        $uniqueId = \uniqid('test-region-', true);

        // 创建 us-east-1 区域快照
        $eastSnapshot1 = $this->createNewEntity();
        $eastSnapshot1->setName('east-snapshot-1-' . $uniqueId);
        $eastSnapshot1->setRegion('us-east-1');
        $eastSnapshot1->setDiskName('disk-east-1-' . $uniqueId);
        self::getEntityManager()->persist($eastSnapshot1);

        // 创建另一个 us-east-1 区域快照
        $eastSnapshot2 = new DiskSnapshot();
        $eastSnapshot2->setName('east-snapshot-2-' . $uniqueId);
        $eastSnapshot2->setArn('arn:aws:lightsail:us-east-1:123456789012:DiskSnapshot/east-snapshot-2-' . $uniqueId);
        $eastSnapshot2->setRegion('us-east-1');
        $eastSnapshot2->setDiskName('disk-east-2-' . $uniqueId);
        $eastSnapshot2->setSizeInGb(64);
        $eastSnapshot2->setCredential($this->createTestAwsCredential());
        self::getEntityManager()->persist($eastSnapshot2);

        // 创建 us-west-2 区域快照
        $westSnapshot = new DiskSnapshot();
        $westSnapshot->setName('west-snapshot-1-' . $uniqueId);
        $westSnapshot->setArn('arn:aws:lightsail:us-west-2:123456789012:DiskSnapshot/west-snapshot-1-' . $uniqueId);
        $westSnapshot->setRegion('us-west-2');
        $westSnapshot->setDiskName('disk-west-' . $uniqueId);
        $westSnapshot->setSizeInGb(32);
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
        $this->assertContainsOnlyInstancesOf(DiskSnapshot::class, $eastSnapshots);
        $eastNames = \array_map(fn ($s) => $s->getName(), $eastSnapshots);
        $this->assertContains('east-snapshot-1-' . $uniqueId, $eastNames);
        $this->assertContains('east-snapshot-2-' . $uniqueId, $eastNames);

        // 测试查找 us-west-2 区域的快照（只验证我们创建的）
        $westSnapshots = $this->getRepository()->findByRegion('us-west-2');
        $this->assertGreaterThanOrEqual(1, \count($westSnapshots));
        $westNames = \array_map(fn ($s) => $s->getName(), $westSnapshots);
        $this->assertContains('west-snapshot-1-' . $uniqueId, $westNames);
    }

    /**
     * 测试按大小范围查找快照
     */
    public function testFindBySizeRange(): void
    {
        $uniqueId = \uniqid('test-size-', true);

        // 创建不同大小的快照
        $snapshot32 = $this->createNewEntity();
        $snapshot32->setName('snapshot-32gb-' . $uniqueId);
        $snapshot32->setDiskName('disk-32-' . $uniqueId);
        $snapshot32->setSizeInGb(32);
        self::getEntityManager()->persist($snapshot32);

        $snapshot64 = new DiskSnapshot();
        $snapshot64->setName('snapshot-64gb-' . $uniqueId);
        $snapshot64->setArn('arn:aws:lightsail:us-east-1:123456789012:DiskSnapshot/snapshot-64gb-' . $uniqueId);
        $snapshot64->setRegion('us-east-1');
        $snapshot64->setDiskName('disk-64-' . $uniqueId);
        $snapshot64->setSizeInGb(64);
        $snapshot64->setCredential($this->createTestAwsCredential());
        self::getEntityManager()->persist($snapshot64);

        $snapshot128 = new DiskSnapshot();
        $snapshot128->setName('snapshot-128gb-' . $uniqueId);
        $snapshot128->setArn('arn:aws:lightsail:us-east-1:123456789012:DiskSnapshot/snapshot-128gb-' . $uniqueId);
        $snapshot128->setRegion('us-east-1');
        $snapshot128->setDiskName('disk-128-' . $uniqueId);
        $snapshot128->setSizeInGb(128);
        $snapshot128->setCredential($this->createTestAwsCredential());
        self::getEntityManager()->persist($snapshot128);

        $snapshot256 = new DiskSnapshot();
        $snapshot256->setName('snapshot-256gb-' . $uniqueId);
        $snapshot256->setArn('arn:aws:lightsail:us-east-1:123456789012:DiskSnapshot/snapshot-256gb-' . $uniqueId);
        $snapshot256->setRegion('us-east-1');
        $snapshot256->setDiskName('disk-256-' . $uniqueId);
        $snapshot256->setSizeInGb(256);
        $snapshot256->setCredential($this->createTestAwsCredential());
        self::getEntityManager()->persist($snapshot256);

        self::getEntityManager()->flush();

        // 测试只指定最小值（>= 32GB）
        $allLargeSnapshots = $this->getRepository()->findBySizeRange(32);
        $this->assertGreaterThanOrEqual(4, \count($allLargeSnapshots));
        $allNames = \array_map(fn ($s) => $s->getName(), $allLargeSnapshots);
        $this->assertContains('snapshot-32gb-' . $uniqueId, $allNames);
        $this->assertContains('snapshot-64gb-' . $uniqueId, $allNames);
        $this->assertContains('snapshot-128gb-' . $uniqueId, $allNames);
        $this->assertContains('snapshot-256gb-' . $uniqueId, $allNames);

        // 测试只查找 >= 64GB 的快照
        $largeSnapshots = $this->getRepository()->findBySizeRange(64);
        $this->assertGreaterThanOrEqual(3, \count($largeSnapshots));
        $largeNames = \array_map(fn ($s) => $s->getName(), $largeSnapshots);
        $this->assertContains('snapshot-64gb-' . $uniqueId, $largeNames);
        $this->assertContains('snapshot-128gb-' . $uniqueId, $largeNames);
        $this->assertContains('snapshot-256gb-' . $uniqueId, $largeNames);
        $this->assertNotContains('snapshot-32gb-' . $uniqueId, $largeNames);

        // 测试查找 64GB - 128GB 范围的快照
        $mediumSnapshots = $this->getRepository()->findBySizeRange(64, 128);
        $this->assertGreaterThanOrEqual(2, \count($mediumSnapshots));
        foreach ($mediumSnapshots as $snapshot) {
            $this->assertGreaterThanOrEqual(64, $snapshot->getSizeInGb());
            $this->assertLessThanOrEqual(128, $snapshot->getSizeInGb());
        }
        $mediumNames = \array_map(fn ($s) => $s->getName(), $mediumSnapshots);
        $this->assertContains('snapshot-64gb-' . $uniqueId, $mediumNames);
        $this->assertContains('snapshot-128gb-' . $uniqueId, $mediumNames);

        // 测试精确范围（100-200GB，应该只包含128GB的）
        $exactRangeSnapshots = $this->getRepository()->findBySizeRange(100, 200);
        $exactNames          = \array_map(fn ($s) => $s->getName(), $exactRangeSnapshots);
        $this->assertContains('snapshot-128gb-' . $uniqueId, $exactNames);
        $this->assertNotContains('snapshot-64gb-' . $uniqueId, $exactNames);
        $this->assertNotContains('snapshot-256gb-' . $uniqueId, $exactNames);

        // 测试空结果（使用超大值）
        $emptyResult = $this->getRepository()->findBySizeRange(10000);
        $this->assertCount(0, $emptyResult);
    }
}
