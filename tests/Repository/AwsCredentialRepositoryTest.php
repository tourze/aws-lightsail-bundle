<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Repository;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Repository\AwsCredentialRepository;
use Doctrine\DBAL\Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(AwsCredentialRepository::class)]
#[RunTestsInSeparateProcesses]
final class AwsCredentialRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 初始化逻辑
    }

    protected function createNewEntity(): AwsCredential
    {
        $credential = new AwsCredential();
        $credential->setName('test-credential');
        $credential->setAccessKeyId('test-access-key');
        $credential->setSecretAccessKey('test-secret-key');
        $credential->setRegion('us-east-1');
        $credential->setIsDefault(false);

        return $credential;
    }

    protected function getRepository(): AwsCredentialRepository
    {
        $repository = self::getContainer()->get(AwsCredentialRepository::class);
        $this->assertInstanceOf(AwsCredentialRepository::class, $repository);

        return $repository;
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
        $this->assertSame('test-access-key', $foundEntity->getAccessKeyId());
    }

    /**
     * 测试保存和检索实体
     */
    public function testSaveAndRetrieveEntity(): void
    {
        $entity     = $this->createNewEntity();
        $repository = $this->getRepository();

        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();
        $this->assertNotNull($entity->getId());

        $foundEntity = $repository->find($entity->getId());
        $this->assertNotNull($foundEntity);
        $this->assertSame('test-access-key', $foundEntity->getAccessKeyId());
        $this->assertSame('test-secret-key', $foundEntity->getSecretAccessKey());
        $this->assertSame('us-east-1', $foundEntity->getRegion());
        $this->assertFalse($foundEntity->isDefault());
    }

    /**
     * 测试查找默认凭证
     */
    public function testFindDefault(): void
    {
        $repository = $this->getRepository();

        // 清除现有的默认凭证
        $existingDefaults = $repository->findBy(['isDefault' => true]);
        foreach ($existingDefaults as $existing) {
            $existing->setIsDefault(false);
        }
        self::getEntityManager()->flush();

        // 创建非默认凭证
        $credential1 = $this->createNewEntity();
        $this->assertInstanceOf(AwsCredential::class, $credential1);
        $credential1->setIsDefault(false);
        self::getEntityManager()->persist($credential1);

        // 创建默认凭证
        $credential2 = new AwsCredential();
        $credential2->setName('default-credential');
        $credential2->setAccessKeyId('default-access-key');
        $credential2->setSecretAccessKey('default-secret-key');
        $credential2->setRegion('us-east-1');
        $credential2->setIsDefault(true);
        self::getEntityManager()->persist($credential2);
        self::getEntityManager()->flush();

        $defaultCredential = $repository->findDefault();
        $this->assertNotNull($defaultCredential);
        $this->assertSame('default-access-key', $defaultCredential->getAccessKeyId());
        $this->assertTrue($defaultCredential->isDefault());
    }

    /**
     * 测试删除实体
     */
    public function testRemoveEntity(): void
    {
        $entity     = $this->createNewEntity();
        $repository = $this->getRepository();

        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();
        $entityId = $entity->getId();

        self::getEntityManager()->remove($entity);
        self::getEntityManager()->flush();

        $foundEntity = $repository->find($entityId);
        $this->assertNull($foundEntity);
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
