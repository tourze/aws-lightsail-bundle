<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Repository;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Bucket;
use AwsLightsailBundle\Enum\BucketAccessRuleEnum;
use AwsLightsailBundle\Repository\BucketRepository;
use Doctrine\DBAL\Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(BucketRepository::class)]
#[RunTestsInSeparateProcesses]
final class BucketRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 初始化逻辑
    }

    protected function createNewEntity(): Bucket
    {
        $bucket = new Bucket();
        $bucket->setName('test-bucket');
        $bucket->setArn('arn:aws:lightsail:us-east-1:123456789012:Bucket/test-bucket');
        $bucket->setRegion('us-east-1');
        $bucket->setBundleId('small_1_0');
        $bucket->setUrl('https://test-bucket.s3.amazonaws.com');
        $bucket->setCredential($this->createTestAwsCredential());

        return $bucket;
    }

    protected function getRepository(): BucketRepository
    {
        $repository = self::getContainer()->get(BucketRepository::class);
        $this->assertInstanceOf(BucketRepository::class, $repository);

        return $repository;
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
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $foundEntity = $this->getRepository()->find($entity->getId());
        $this->assertNotNull($foundEntity);
        $this->assertSame('test-bucket', $foundEntity->getName());
    }

    /**
     * 测试保存和检索实体
     */
    public function testSaveAndRetrieveEntity(): void
    {
        $entity = $this->createNewEntity();
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $foundEntity = $this->getRepository()->findOneBy(['name' => 'test-bucket']);
        $this->assertNotNull($foundEntity);
        $this->assertSame('us-east-1', $foundEntity->getRegion());
    }

    /**
     * 测试按区域查找存储桶
     */
    public function testFindByRegion(): void
    {
        $entity = $this->createNewEntity();
        $entity->setRegion('eu-central-1');
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByRegion('eu-central-1');
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result), 'Should find at least our inserted bucket');

        // 验证所有返回的存储桶都属于正确的区域
        foreach ($result as $bucket) {
            $this->assertInstanceOf(Bucket::class, $bucket);
            $this->assertSame('eu-central-1', $bucket->getRegion());
        }

        // 验证我们插入的存储桶在结果中
        $foundOurBucket = false;
        foreach ($result as $bucket) {
            if ($bucket->getName() === $entity->getName() && $bucket->getArn() === $entity->getArn()) {
                $foundOurBucket = true;

                break;
            }
        }
        $this->assertTrue($foundOurBucket, 'Our inserted bucket should be found in the results');
    }

    /**
     * 测试按访问规则查找存储桶
     */
    public function testFindByAccessRule(): void
    {
        $entity = $this->createNewEntity();
        $entity->setAccessRules(BucketAccessRuleEnum::PUBLIC_READ);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByAccessRule(BucketAccessRuleEnum::PUBLIC_READ);
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result), 'Should find at least our inserted bucket');

        // 验证所有返回的存储桶都有正确的访问规则
        foreach ($result as $bucket) {
            $this->assertInstanceOf(Bucket::class, $bucket);
            $this->assertSame(BucketAccessRuleEnum::PUBLIC_READ, $bucket->getAccessRules());
        }

        // 验证我们插入的存储桶在结果中
        $foundOurBucket = false;
        foreach ($result as $bucket) {
            if ($bucket->getName() === $entity->getName() && $bucket->getArn() === $entity->getArn()) {
                $foundOurBucket = true;

                break;
            }
        }
        $this->assertTrue($foundOurBucket, 'Our inserted bucket should be found in the results');
    }

    /**
     * 测试查找大于指定大小的存储桶
     */
    public function testFindLargerThan(): void
    {
        $entity = $this->createNewEntity();
        $entity->setSizeInMb(2048);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findLargerThan(1024);
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result), 'Should find at least our inserted large bucket');

        // 验证所有返回的存储桶都大于指定大小
        foreach ($result as $bucket) {
            $this->assertInstanceOf(Bucket::class, $bucket);
            $this->assertGreaterThan(1024, $bucket->getSizeInMb());
        }

        // 验证我们插入的存储桶在结果中
        $foundOurBucket = false;
        foreach ($result as $bucket) {
            if ($bucket->getName() === $entity->getName() && $bucket->getArn() === $entity->getArn()) {
                $foundOurBucket = true;

                break;
            }
        }
        $this->assertTrue($foundOurBucket, 'Our inserted large bucket should be found in the results');
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
