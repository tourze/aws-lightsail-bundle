<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Repository;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\LoadBalancer;
use AwsLightsailBundle\Enum\LoadBalancerStatusEnum;
use AwsLightsailBundle\Repository\LoadBalancerRepository;
use Doctrine\DBAL\Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(LoadBalancerRepository::class)]
#[RunTestsInSeparateProcesses]
final class LoadBalancerRepositoryTest extends AbstractRepositoryTestCase
{
    protected function createNewEntity(): LoadBalancer
    {
        $loadBalancer = new LoadBalancer();
        $loadBalancer->setName('test-loadbalancer');
        $loadBalancer->setArn('arn:aws:lightsail:us-east-1:123456789012:LoadBalancer/test-loadbalancer');
        $loadBalancer->setRegion('us-east-1');
        $loadBalancer->setDnsName('test-lb.example.com');
        $loadBalancer->setHealthCheckPort(80);
        $loadBalancer->setHealthCheckProtocol('HTTP');
        $loadBalancer->setHealthCheckPath('/');
        $loadBalancer->setHealthCheckIntervalSeconds(30);
        $loadBalancer->setHealthCheckTimeoutSeconds(5);
        $loadBalancer->setHealthyThreshold(2);
        $loadBalancer->setUnhealthyThreshold(3);
        $loadBalancer->setCredential($this->createTestAwsCredential());

        return $loadBalancer;
    }

    protected function getRepository(): LoadBalancerRepository
    {
        $repository = self::getContainer()->get(LoadBalancerRepository::class);
        $this->assertInstanceOf(LoadBalancerRepository::class, $repository);

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
        $this->assertInstanceOf(LoadBalancer::class, $entity);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $foundEntity = $this->getRepository()->find($entity->getId());
        $this->assertNotNull($foundEntity);
        $this->assertInstanceOf(LoadBalancer::class, $foundEntity);
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

        $foundEntity = $this->getRepository()->findOneBy(['name' => 'test-loadbalancer']);
        $this->assertNotNull($foundEntity);
        $this->assertSame('us-east-1', $foundEntity->getRegion());
    }

    /**
     * 测试按状态查找负载均衡器
     */
    public function testFindByStatus(): void
    {
        $entity = $this->createNewEntity();
        $entity->setName('unique-status-test-lb');
        $entity->setStatus(LoadBalancerStatusEnum::ACTIVE);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByStatus(LoadBalancerStatusEnum::ACTIVE);
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result));
        $found = false;
        foreach ($result as $lb) {
            $this->assertInstanceOf(LoadBalancer::class, $lb);
            if ('unique-status-test-lb' === $lb->getName()) {
                $this->assertSame(LoadBalancerStatusEnum::ACTIVE, $lb->getStatus());
                $found = true;

                break;
            }
        }
        $this->assertTrue($found, '未找到预期的负载均衡器实体');
    }

    /**
     * 测试按区域查找负载均衡器
     */
    public function testFindByRegion(): void
    {
        $entity = $this->createNewEntity();
        $entity->setName('unique-region-test-lb');
        $entity->setRegion('us-west-2');
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByRegion('us-west-2');
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result));
        $found = false;
        foreach ($result as $lb) {
            $this->assertInstanceOf(LoadBalancer::class, $lb);
            if ('unique-region-test-lb' === $lb->getName()) {
                $this->assertSame('us-west-2', $lb->getRegion());
                $found = true;

                break;
            }
        }
        $this->assertTrue($found, '未找到预期的负载均衡器实体');
    }

    /**
     * 测试按证书名称查找负载均衡器
     */
    public function testFindByCertificate(): void
    {
        $entity = $this->createNewEntity();
        $entity->setName('unique-cert-test-lb');
        $entity->setTlsCertificateName('test-cert-unique');
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByCertificate('test-cert-unique');
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result));
        $found = false;
        foreach ($result as $lb) {
            $this->assertInstanceOf(LoadBalancer::class, $lb);
            if ('unique-cert-test-lb' === $lb->getName()) {
                $this->assertSame('test-cert-unique', $lb->getTlsCertificateName());
                $found = true;

                break;
            }
        }
        $this->assertTrue($found, '未找到预期的负载均衡器实体');
    }

    /**
     * 测试查找启用了HTTPS的负载均衡器
     */
    public function testFindWithHttpsEnabled(): void
    {
        $entity = $this->createNewEntity();
        $entity->setName('unique-https-test-lb');
        $entity->setTlsPolicyEnabled(true);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findWithHttpsEnabled();
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result));
        $found = false;
        foreach ($result as $lb) {
            $this->assertInstanceOf(LoadBalancer::class, $lb);
            if ('unique-https-test-lb' === $lb->getName()) {
                $this->assertTrue($lb->isTlsPolicyEnabled());
                $found = true;

                break;
            }
        }
        $this->assertTrue($found, '未找到预期的负载均衡器实体');
    }

    /**
     * 测试按实例名称查找负载均衡器
     */
    public function testFindByInstanceName(): void
    {
        $entity = $this->createNewEntity();
        $entity->setName('unique-instance-test-lb');
        $entity->setAttachedInstances(['unique-test-instance-1', 'unique-test-instance-2']);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByInstanceName('unique-test-instance-1');
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result));
        $found = false;
        foreach ($result as $lb) {
            $this->assertInstanceOf(LoadBalancer::class, $lb);
            if ('unique-instance-test-lb' === $lb->getName()) {
                $this->assertContains('unique-test-instance-1', $lb->getAttachedInstances());
                $found = true;

                break;
            }
        }
        $this->assertTrue($found, '未找到预期的负载均衡器实体');
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
}
