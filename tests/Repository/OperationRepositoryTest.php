<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Repository;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Operation;
use AwsLightsailBundle\Enum\OperationStatusEnum;
use AwsLightsailBundle\Enum\OperationTypeEnum;
use AwsLightsailBundle\Repository\OperationRepository;
use Doctrine\DBAL\Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(OperationRepository::class)]
#[RunTestsInSeparateProcesses]
final class OperationRepositoryTest extends AbstractRepositoryTestCase
{
    protected function createNewEntity(): Operation
    {
        $operation = new Operation();
        $operation->setOperationId('test-operation-id');
        $operation->setType(OperationTypeEnum::CREATE_INSTANCE);
        $operation->setStatus(OperationStatusEnum::SUCCEEDED);
        $operation->setRegion('us-east-1');
        $operation->setCredential($this->createTestAwsCredential());

        return $operation;
    }

    protected function getRepository(): OperationRepository
    {
        $repository = self::getContainer()->get(OperationRepository::class);
        $this->assertInstanceOf(OperationRepository::class, $repository);

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
        $this->assertInstanceOf(Operation::class, $entity);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $foundEntity = $this->getRepository()->find($entity->getId());
        $this->assertNotNull($foundEntity);
        $this->assertInstanceOf(Operation::class, $foundEntity);
        $this->assertSame($entity->getType(), $foundEntity->getType());
    }

    /**
     * 测试保存和检索实体
     */
    public function testSaveAndRetrieveEntity(): void
    {
        $entity = $this->createNewEntity();
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $foundEntity = $this->getRepository()->find($entity->getId());
        $this->assertNotNull($foundEntity);
        $this->assertSame(OperationStatusEnum::SUCCEEDED, $foundEntity->getStatus());
        $this->assertSame(OperationTypeEnum::CREATE_INSTANCE, $foundEntity->getType());
        $this->assertSame('test-operation-id', $foundEntity->getOperationId());
    }

    /**
     * 测试按状态查找操作
     */
    public function testFindByStatus(): void
    {
        $entity = $this->createNewEntity();
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByStatus(OperationStatusEnum::SUCCEEDED);

        $this->assertGreaterThanOrEqual(1, \count($result));
        $this->assertInstanceOf(Operation::class, $result[0]);
        $this->assertSame(OperationStatusEnum::SUCCEEDED, $result[0]->getStatus());
    }

    /**
     * 测试按类型查找操作
     */
    public function testFindByType(): void
    {
        $entity = $this->createNewEntity();
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByType(OperationTypeEnum::CREATE_INSTANCE);

        $this->assertGreaterThanOrEqual(1, \count($result));
        $this->assertInstanceOf(Operation::class, $result[0]);
        $this->assertSame(OperationTypeEnum::CREATE_INSTANCE, $result[0]->getType());
    }

    /**
     * 测试按资源名称查找操作
     */
    public function testFindByResourceName(): void
    {
        $entity = $this->createNewEntity();
        $entity->setResourceName('test-resource');
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByResourceName('test-resource');

        $this->assertGreaterThanOrEqual(1, \count($result));
        $this->assertInstanceOf(Operation::class, $result[0]);
        $this->assertSame('test-resource', $result[0]->getResourceName());
    }

    /**
     * 测试按区域查找操作
     */
    public function testFindByRegion(): void
    {
        $entity = $this->createNewEntity();
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByRegion('us-east-1');

        $this->assertGreaterThanOrEqual(1, \count($result));
        $this->assertInstanceOf(Operation::class, $result[0]);
        $this->assertSame('us-east-1', $result[0]->getRegion());
    }

    /**
     * 测试查找最近的操作
     */
    public function testFindRecent(): void
    {
        $entity = $this->createNewEntity();
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findRecent(5);

        $this->assertGreaterThanOrEqual(1, \count($result));
        $this->assertInstanceOf(Operation::class, $result[0]);
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
