<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Repository;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\ContainerService;
use AwsLightsailBundle\Enum\ContainerServicePowerEnum;
use AwsLightsailBundle\Enum\ContainerServiceStateEnum;
use AwsLightsailBundle\Repository\ContainerServiceRepository;
use Doctrine\DBAL\Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(ContainerServiceRepository::class)]
#[RunTestsInSeparateProcesses]
final class ContainerServiceRepositoryTest extends AbstractRepositoryTestCase
{
    protected function createNewEntity(): ContainerService
    {
        $containerService = new ContainerService();
        $containerService->setName('test-container');
        $containerService->setArn('arn:aws:lightsail:us-east-1:123456789012:ContainerService/test-container');
        $containerService->setRegion('us-east-1');
        $containerService->setPower(ContainerServicePowerEnum::NANO);
        $containerService->setScale(1);
        $containerService->setCredential($this->createTestAwsCredential());

        return $containerService;
    }

    protected function getRepository(): ContainerServiceRepository
    {
        $repository = self::getContainer()->get(ContainerServiceRepository::class);
        $this->assertInstanceOf(ContainerServiceRepository::class, $repository);

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
        $this->assertInstanceOf(ContainerService::class, $entity);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $foundEntity = $this->getRepository()->find($entity->getId());
        $this->assertNotNull($foundEntity);
        $this->assertInstanceOf(ContainerService::class, $foundEntity);
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

        $foundEntity = $this->getRepository()->findOneBy(['name' => 'test-container']);
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
     * 测试按最小副本数查找容器服务
     */
    public function testFindByMinimumScale(): void
    {
        $uniqueId = \uniqid('test-scale-', true);

        // 创建 scale=1 的服务
        $service1 = $this->createNewEntity();
        $service1->setName('service-scale-1-' . $uniqueId);
        $service1->setScale(1);
        $service1->setArn('arn:aws:lightsail:us-east-1:123456789012:ContainerService/service-scale-1-' . $uniqueId);
        self::getEntityManager()->persist($service1);

        // 创建 scale=3 的服务
        $service2 = new ContainerService();
        $service2->setName('service-scale-3-' . $uniqueId);
        $service2->setArn('arn:aws:lightsail:us-east-1:123456789012:ContainerService/service-scale-3-' . $uniqueId);
        $service2->setRegion('us-east-1');
        $service2->setPower(ContainerServicePowerEnum::NANO);
        $service2->setScale(3);
        $service2->setCredential($this->createTestAwsCredential());
        self::getEntityManager()->persist($service2);

        // 创建 scale=5 的服务
        $service3 = new ContainerService();
        $service3->setName('service-scale-5-' . $uniqueId);
        $service3->setArn('arn:aws:lightsail:us-east-1:123456789012:ContainerService/service-scale-5-' . $uniqueId);
        $service3->setRegion('us-east-1');
        $service3->setPower(ContainerServicePowerEnum::MICRO);
        $service3->setScale(5);
        $service3->setCredential($this->createTestAwsCredential());
        self::getEntityManager()->persist($service3);

        self::getEntityManager()->flush();

        // 测试查找 scale >= 3 的服务
        $results = $this->getRepository()->findByMinimumScale(3);
        $this->assertGreaterThanOrEqual(2, \count($results));
        $this->assertContainsOnlyInstancesOf(ContainerService::class, $results);

        // 验证我们创建的服务在结果中
        $names = \array_map(fn ($s) => $s->getName(), $results);
        $this->assertContains('service-scale-3-' . $uniqueId, $names);
        $this->assertContains('service-scale-5-' . $uniqueId, $names);
        $this->assertNotContains('service-scale-1-' . $uniqueId, $names);

        // 验证排序：按 scale DESC
        $this->assertGreaterThanOrEqual($results[1]->getScale(), $results[0]->getScale());
    }

    /**
     * 测试按性能等级查找容器服务
     */
    public function testFindByPower(): void
    {
        $uniqueId = \uniqid('test-power-', true);

        // 创建 NANO 性能服务
        $nanoService1 = $this->createNewEntity();
        $nanoService1->setName('nano-service-1-' . $uniqueId);
        $nanoService1->setPower(ContainerServicePowerEnum::NANO);
        $nanoService1->setArn('arn:aws:lightsail:us-east-1:123456789012:ContainerService/nano-service-1-' . $uniqueId);
        self::getEntityManager()->persist($nanoService1);

        // 创建另一个 NANO 性能服务
        $nanoService2 = new ContainerService();
        $nanoService2->setName('nano-service-2-' . $uniqueId);
        $nanoService2->setArn('arn:aws:lightsail:us-east-1:123456789012:ContainerService/nano-service-2-' . $uniqueId);
        $nanoService2->setRegion('us-east-1');
        $nanoService2->setPower(ContainerServicePowerEnum::NANO);
        $nanoService2->setScale(1);
        $nanoService2->setCredential($this->createTestAwsCredential());
        self::getEntityManager()->persist($nanoService2);

        // 创建 MICRO 性能服务
        $microService = new ContainerService();
        $microService->setName('micro-service-1-' . $uniqueId);
        $microService->setArn('arn:aws:lightsail:us-east-1:123456789012:ContainerService/micro-service-1-' . $uniqueId);
        $microService->setRegion('us-east-1');
        $microService->setPower(ContainerServicePowerEnum::MICRO);
        $microService->setScale(1);
        $microService->setCredential($this->createTestAwsCredential());
        self::getEntityManager()->persist($microService);

        self::getEntityManager()->flush();

        // 测试查找 NANO 服务
        $nanoResults = $this->getRepository()->findByPower(ContainerServicePowerEnum::NANO);
        $this->assertGreaterThanOrEqual(2, \count($nanoResults));
        $this->assertContainsOnlyInstancesOf(ContainerService::class, $nanoResults);

        // 验证我们创建的 NANO 服务在结果中
        $nanoNames = \array_map(fn ($s) => $s->getName(), $nanoResults);
        $this->assertContains('nano-service-1-' . $uniqueId, $nanoNames);
        $this->assertContains('nano-service-2-' . $uniqueId, $nanoNames);

        // 测试查找 MICRO 服务
        $microResults = $this->getRepository()->findByPower(ContainerServicePowerEnum::MICRO);
        $this->assertGreaterThanOrEqual(1, \count($microResults));
        $microNames = \array_map(fn ($s) => $s->getName(), $microResults);
        $this->assertContains('micro-service-1-' . $uniqueId, $microNames);
    }

    /**
     * 测试按区域查找容器服务
     */
    public function testFindByRegion(): void
    {
        $uniqueId = \uniqid('test-region-', true);

        // 创建 us-east-1 区域服务
        $eastService1 = $this->createNewEntity();
        $eastService1->setName('east-service-1-' . $uniqueId);
        $eastService1->setRegion('us-east-1');
        $eastService1->setArn('arn:aws:lightsail:us-east-1:123456789012:ContainerService/east-service-1-' . $uniqueId);
        self::getEntityManager()->persist($eastService1);

        // 创建另一个 us-east-1 区域服务
        $eastService2 = new ContainerService();
        $eastService2->setName('east-service-2-' . $uniqueId);
        $eastService2->setArn('arn:aws:lightsail:us-east-1:123456789012:ContainerService/east-service-2-' . $uniqueId);
        $eastService2->setRegion('us-east-1');
        $eastService2->setPower(ContainerServicePowerEnum::NANO);
        $eastService2->setScale(1);
        $eastService2->setCredential($this->createTestAwsCredential());
        self::getEntityManager()->persist($eastService2);

        // 创建 us-west-2 区域服务
        $westService = new ContainerService();
        $westService->setName('west-service-1-' . $uniqueId);
        $westService->setArn('arn:aws:lightsail:us-west-2:123456789012:ContainerService/west-service-1-' . $uniqueId);
        $westService->setRegion('us-west-2');
        $westService->setPower(ContainerServicePowerEnum::NANO);
        $westService->setScale(1);
        $credential = new AwsCredential();
        $credential->setName('west-credential-' . $uniqueId);
        $credential->setAccessKeyId('test-access-key');
        $credential->setSecretAccessKey('test-secret-key');
        $credential->setRegion('us-west-2');
        $westService->setCredential($credential);
        self::getEntityManager()->persist($westService);

        self::getEntityManager()->flush();

        // 测试查找 us-east-1 区域服务
        $eastResults = $this->getRepository()->findByRegion('us-east-1');
        $this->assertGreaterThanOrEqual(2, \count($eastResults));
        $this->assertContainsOnlyInstancesOf(ContainerService::class, $eastResults);
        $eastNames = \array_map(fn ($s) => $s->getName(), $eastResults);
        $this->assertContains('east-service-1-' . $uniqueId, $eastNames);
        $this->assertContains('east-service-2-' . $uniqueId, $eastNames);

        // 测试查找 us-west-2 区域服务
        $westResults = $this->getRepository()->findByRegion('us-west-2');
        $this->assertGreaterThanOrEqual(1, \count($westResults));
        $westNames = \array_map(fn ($s) => $s->getName(), $westResults);
        $this->assertContains('west-service-1-' . $uniqueId, $westNames);

        // 测试查找不存在的区域
        $emptyResult = $this->getRepository()->findByRegion('test-nonexistent-' . $uniqueId);
        $this->assertCount(0, $emptyResult);
    }

    /**
     * 测试按状态查找容器服务
     */
    public function testFindByState(): void
    {
        $uniqueId = \uniqid('test-state-', true);

        // 创建 RUNNING 状态服务
        $runningService1 = $this->createNewEntity();
        $runningService1->setName('running-service-1-' . $uniqueId);
        $runningService1->setState(ContainerServiceStateEnum::RUNNING);
        $runningService1->setArn('arn:aws:lightsail:us-east-1:123456789012:ContainerService/running-service-1-' . $uniqueId);
        self::getEntityManager()->persist($runningService1);

        // 创建另一个 RUNNING 状态服务
        $runningService2 = new ContainerService();
        $runningService2->setName('running-service-2-' . $uniqueId);
        $runningService2->setArn('arn:aws:lightsail:us-east-1:123456789012:ContainerService/running-service-2-' . $uniqueId);
        $runningService2->setRegion('us-east-1');
        $runningService2->setPower(ContainerServicePowerEnum::NANO);
        $runningService2->setScale(1);
        $runningService2->setState(ContainerServiceStateEnum::RUNNING);
        $runningService2->setCredential($this->createTestAwsCredential());
        self::getEntityManager()->persist($runningService2);

        // 创建 PENDING 状态服务
        $pendingService = new ContainerService();
        $pendingService->setName('pending-service-1-' . $uniqueId);
        $pendingService->setArn('arn:aws:lightsail:us-east-1:123456789012:ContainerService/pending-service-1-' . $uniqueId);
        $pendingService->setRegion('us-east-1');
        $pendingService->setPower(ContainerServicePowerEnum::NANO);
        $pendingService->setScale(1);
        $pendingService->setState(ContainerServiceStateEnum::PENDING);
        $pendingService->setCredential($this->createTestAwsCredential());
        self::getEntityManager()->persist($pendingService);

        self::getEntityManager()->flush();

        // 测试查找 RUNNING 状态服务
        $runningResults = $this->getRepository()->findByState(ContainerServiceStateEnum::RUNNING);
        $this->assertGreaterThanOrEqual(2, \count($runningResults));
        $this->assertContainsOnlyInstancesOf(ContainerService::class, $runningResults);

        // 验证我们创建的 RUNNING 服务在结果中
        $runningNames = \array_map(fn ($s) => $s->getName(), $runningResults);
        $this->assertContains('running-service-1-' . $uniqueId, $runningNames);
        $this->assertContains('running-service-2-' . $uniqueId, $runningNames);

        // 测试查找 PENDING 状态服务
        $pendingResults = $this->getRepository()->findByState(ContainerServiceStateEnum::PENDING);
        $this->assertGreaterThanOrEqual(1, \count($pendingResults));
        $pendingNames = \array_map(fn ($s) => $s->getName(), $pendingResults);
        $this->assertContains('pending-service-1-' . $uniqueId, $pendingNames);
    }
}
