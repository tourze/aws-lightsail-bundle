<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Repository;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Disk;
use AwsLightsailBundle\Enum\DiskStateEnum;
use AwsLightsailBundle\Repository\DiskRepository;
use Doctrine\DBAL\Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(DiskRepository::class)]
#[RunTestsInSeparateProcesses]
final class DiskRepositoryTest extends AbstractRepositoryTestCase
{
    protected function createNewEntity(): Disk
    {
        $disk = new Disk();
        $disk->setName('test-disk');
        $disk->setArn('arn:aws:lightsail:us-east-1:123456789012:Disk/test-disk');
        $disk->setRegion('us-east-1');
        $disk->setSizeInGb(32);
        $disk->setCredential($this->createTestAwsCredential());

        return $disk;
    }

    protected function getRepository(): DiskRepository
    {
        $repository = self::getContainer()->get(DiskRepository::class);
        $this->assertInstanceOf(DiskRepository::class, $repository);

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
        $this->assertInstanceOf(Disk::class, $entity);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $foundEntity = $this->getRepository()->find($entity->getId());
        $this->assertNotNull($foundEntity);
        $this->assertInstanceOf(Disk::class, $foundEntity);
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

        $foundEntity = $this->getRepository()->findOneBy(['name' => 'test-disk']);
        $this->assertNotNull($foundEntity);
        $this->assertSame('us-east-1', $foundEntity->getRegion());
    }

    /**
     * 测试按状态查找磁盘
     */
    public function testFindByState(): void
    {
        $entity = $this->createNewEntity();
        $entity->setState(DiskStateEnum::AVAILABLE);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByState(DiskStateEnum::AVAILABLE);
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, count($result), 'Should find at least the disk we created');

        // 验证我们创建的实体在结果中
        $foundOurEntity = false;
        foreach ($result as $disk) {
            $this->assertInstanceOf(Disk::class, $disk);
            $this->assertSame(DiskStateEnum::AVAILABLE, $disk->getState());
            if ($disk->getId() === $entity->getId()) {
                $foundOurEntity = true;
            }
        }
        $this->assertTrue($foundOurEntity, 'Should find the disk we created');
    }

    /**
     * 测试按区域查找磁盘
     */
    public function testFindByRegion(): void
    {
        $entity = $this->createNewEntity();
        $entity->setRegion('ap-southeast-1');
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByRegion('ap-southeast-1');
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result), 'Should find at least our inserted disk');

        // 验证所有返回的磁盘都属于正确的区域
        foreach ($result as $disk) {
            $this->assertInstanceOf(Disk::class, $disk);
            $this->assertSame('ap-southeast-1', $disk->getRegion());
        }

        // 验证我们插入的磁盘在结果中
        $foundOurDisk = false;
        foreach ($result as $disk) {
            if ($disk->getName() === $entity->getName() && $disk->getArn() === $entity->getArn()) {
                $foundOurDisk = true;

                break;
            }
        }
        $this->assertTrue($foundOurDisk, 'Our inserted disk should be found in the results');
    }

    /**
     * 测试按挂载实例查找磁盘
     */
    public function testFindByAttachedInstance(): void
    {
        $entity = $this->createNewEntity();
        $entity->setAttachedTo('test-instance');
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByAttachedInstance('test-instance');
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(Disk::class, $result[0]);
        $this->assertSame('test-instance', $result[0]->getAttachedTo());
    }

    /**
     * 测试查找未挂载的磁盘
     */
    public function testFindDetachedDisks(): void
    {
        $entity = $this->createNewEntity();
        $entity->setAttachedTo(null);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findDetachedDisks();
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result), 'Should find at least our inserted detached disk');

        // 验证所有返回的磁盘都是未挂载的
        foreach ($result as $disk) {
            $this->assertInstanceOf(Disk::class, $disk);
            $this->assertNull($disk->getAttachedTo());
        }

        // 验证我们插入的磁盘在结果中
        $foundOurDisk = false;
        foreach ($result as $disk) {
            if ($disk->getName() === $entity->getName() && $disk->getArn() === $entity->getArn()) {
                $foundOurDisk = true;

                break;
            }
        }
        $this->assertTrue($foundOurDisk, 'Our inserted detached disk should be found in the results');
    }

    /**
     * 测试查找大于指定大小的磁盘
     */
    public function testFindLargerThan(): void
    {
        $entity = $this->createNewEntity();
        $entity->setSizeInGb(100);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findLargerThan(50);
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result), 'Should find at least our inserted large disk');

        // 验证所有返回的磁盘都大于指定大小
        foreach ($result as $disk) {
            $this->assertInstanceOf(Disk::class, $disk);
            $this->assertGreaterThan(50, $disk->getSizeInGb());
        }

        // 验证我们插入的磁盘在结果中
        $foundOurDisk = false;
        foreach ($result as $disk) {
            if ($disk->getName() === $entity->getName() && $disk->getArn() === $entity->getArn()) {
                $foundOurDisk = true;

                break;
            }
        }
        $this->assertTrue($foundOurDisk, 'Our inserted large disk should be found in the results');
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
