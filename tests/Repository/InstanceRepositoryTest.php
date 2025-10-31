<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Repository;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Instance;
use AwsLightsailBundle\Enum\InstanceBlueprintEnum;
use AwsLightsailBundle\Enum\InstanceBundleEnum;
use AwsLightsailBundle\Enum\InstanceStateEnum;
use AwsLightsailBundle\Repository\InstanceRepository;
use Doctrine\DBAL\Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(InstanceRepository::class)]
#[RunTestsInSeparateProcesses]
final class InstanceRepositoryTest extends AbstractRepositoryTestCase
{
    protected function createNewEntity(): Instance
    {
        $instance = new Instance();
        $instance->setName('test-instance');
        $instance->setArn('arn:aws:lightsail:us-east-1:123456789012:Instance/test-instance');
        $instance->setRegion('us-east-1');
        $instance->setBlueprint(InstanceBlueprintEnum::AMAZON_LINUX_2);
        $instance->setBundle(InstanceBundleEnum::NANO_2_0);
        $instance->setPrivateIpAddress('192.168.1.100');
        $instance->setPublicIpAddress('203.0.113.100');
        $instance->setCredential($this->createTestAwsCredential());

        return $instance;
    }

    protected function getRepository(): InstanceRepository
    {
        $repository = self::getContainer()->get(InstanceRepository::class);
        $this->assertInstanceOf(InstanceRepository::class, $repository);

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
        $this->assertInstanceOf(Instance::class, $entity);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $foundEntity = $this->getRepository()->find($entity->getId());
        $this->assertNotNull($foundEntity);
        $this->assertInstanceOf(Instance::class, $foundEntity);
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

        $foundEntity = $this->getRepository()->findOneBy(['name' => 'test-instance']);
        $this->assertNotNull($foundEntity);
        $this->assertSame('us-east-1', $foundEntity->getRegion());
    }

    /**
     * 测试按名称和凭证查找实例
     */
    public function testFindOneByNameAndCredential(): void
    {
        $credential = $this->createTestAwsCredential();
        self::getEntityManager()->persist($credential);

        $entity = $this->createNewEntity();
        $entity->setCredential($credential);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findOneByNameAndCredential('test-instance', $credential);

        $this->assertNotNull($result);
        $this->assertInstanceOf(Instance::class, $result);
        $this->assertSame('test-instance', $result->getName());
    }

    /**
     * 测试按状态查找实例
     */
    public function testFindByState(): void
    {
        $entity = $this->createNewEntity();
        $entity->setState(InstanceStateEnum::RUNNING);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByState(InstanceStateEnum::RUNNING->value);

        $this->assertGreaterThanOrEqual(1, \count($result));
        $this->assertInstanceOf(Instance::class, $result[0]);
        $this->assertSame(InstanceStateEnum::RUNNING, $result[0]->getState());
    }

    /**
     * 测试按区域查找实例
     */
    public function testFindByRegion(): void
    {
        $entity = $this->createNewEntity();
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByRegion('us-east-1');

        $this->assertGreaterThanOrEqual(1, \count($result));
        $this->assertInstanceOf(Instance::class, $result[0]);
        $this->assertSame('us-east-1', $result[0]->getRegion());
    }

    /**
     * 测试按蓝图类型查找实例
     */
    public function testFindByBlueprint(): void
    {
        $entity = $this->createNewEntity();
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByBlueprint(InstanceBlueprintEnum::AMAZON_LINUX_2->value);

        $this->assertGreaterThanOrEqual(1, \count($result));
        $this->assertInstanceOf(Instance::class, $result[0]);
        $this->assertSame(InstanceBlueprintEnum::AMAZON_LINUX_2, $result[0]->getBlueprint());
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
