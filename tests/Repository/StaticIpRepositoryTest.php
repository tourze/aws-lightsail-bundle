<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Repository;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Instance;
use AwsLightsailBundle\Entity\StaticIp;
use AwsLightsailBundle\Enum\InstanceBlueprintEnum;
use AwsLightsailBundle\Enum\InstanceBundleEnum;
use AwsLightsailBundle\Repository\StaticIpRepository;
use Doctrine\DBAL\Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(StaticIpRepository::class)]
#[RunTestsInSeparateProcesses]
final class StaticIpRepositoryTest extends AbstractRepositoryTestCase
{
    protected function createNewEntity(): StaticIp
    {
        $staticIp = new StaticIp();
        $staticIp->setName('test-static-ip');
        $staticIp->setArn('arn:aws:lightsail:us-east-1:123456789012:StaticIp/test-static-ip');
        $staticIp->setRegion('us-east-1');
        $staticIp->setIpAddress('203.0.113.200');
        $staticIp->setCredential($this->createTestAwsCredential());

        return $staticIp;
    }

    protected function getRepository(): StaticIpRepository
    {
        $repository = self::getContainer()->get(StaticIpRepository::class);
        $this->assertInstanceOf(StaticIpRepository::class, $repository);

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
        $this->assertInstanceOf(StaticIp::class, $entity);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $foundEntity = $this->getRepository()->find($entity->getId());
        $this->assertNotNull($foundEntity);
        $this->assertInstanceOf(StaticIp::class, $foundEntity);
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

        $foundEntity = $this->getRepository()->findOneBy(['name' => 'test-static-ip']);
        $this->assertNotNull($foundEntity);
        $this->assertSame('us-east-1', $foundEntity->getRegion());
    }

    /**
     * 创建测试用的 Instance 实体
     */
    private function createTestInstance(): Instance
    {
        $instance = new Instance();
        $instance->setName('test-instance');
        $instance->setArn('arn:aws:lightsail:us-east-1:123456789012:Instance/test-instance');
        $instance->setRegion('us-east-1');
        $instance->setBlueprint(InstanceBlueprintEnum::AMAZON_LINUX_2);
        $instance->setBundle(InstanceBundleEnum::NANO_2_0);
        $instance->setCredential($this->createTestAwsCredential());

        return $instance;
    }

    /**
     * 测试按区域查找静态IP
     */
    public function testFindByRegion(): void
    {
        $entity = $this->createNewEntity();
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByRegion('us-east-1');

        $this->assertGreaterThanOrEqual(1, \count($result));
        $this->assertInstanceOf(StaticIp::class, $result[0]);
        $this->assertSame('us-east-1', $result[0]->getRegion());
    }

    /**
     * 测试查找已分配的静态IP
     */
    public function testFindAttached(): void
    {
        $entity = $this->createNewEntity();
        $entity->setAttachedTo('attached-instance');
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findAttached();

        $this->assertGreaterThanOrEqual(1, \count($result));
        $this->assertInstanceOf(StaticIp::class, $result[0]);
        $this->assertNotNull($result[0]->getAttachedTo());
    }

    /**
     * 测试查承未分配的静态IP
     */
    public function testFindDetached(): void
    {
        $entity = $this->createNewEntity();
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findDetached();

        $this->assertGreaterThanOrEqual(1, \count($result));
        $this->assertInstanceOf(StaticIp::class, $result[0]);
        $this->assertNull($result[0]->getAttachedTo());
    }

    /**
     * 测试按实例查找静态IP
     */
    public function testFindByInstance(): void
    {
        $credential = $this->createTestAwsCredential();
        self::getEntityManager()->persist($credential);

        $instance = $this->createTestInstance();
        $instance->setCredential($credential);
        self::getEntityManager()->persist($instance);

        $entity = $this->createNewEntity();
        $entity->setCredential($credential);
        $entity->setAttachedTo($instance->getName());
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByInstance($instance);

        $this->assertNotNull($result);
        $this->assertInstanceOf(StaticIp::class, $result);
        $this->assertSame($instance->getName(), $result->getAttachedTo());
    }

    /**
     * 测试按IP地址查找静态IP
     */
    public function testFindByIpAddress(): void
    {
        $entity = $this->createNewEntity();
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByIpAddress('203.0.113.200');

        $this->assertNotNull($result);
        $this->assertInstanceOf(StaticIp::class, $result);
        $this->assertSame('203.0.113.200', $result->getIpAddress());
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
