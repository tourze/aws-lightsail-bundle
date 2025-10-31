<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Repository;

use AwsLightsailBundle\Entity\Alarm;
use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Enum\AlarmMetricEnum;
use AwsLightsailBundle\Enum\AlarmStateEnum;
use AwsLightsailBundle\Repository\AlarmRepository;
use Doctrine\DBAL\Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(AlarmRepository::class)]
#[RunTestsInSeparateProcesses]
final class AlarmRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 测试设置逻辑
    }

    protected function createNewEntity(): Alarm
    {
        $credential = $this->createTestAwsCredential();
        self::getEntityManager()->persist($credential);

        $alarm = new Alarm();
        $alarm->setName('Test Alarm');
        $alarm->setArn('arn:aws:lightsail:us-east-1:123456789012:Alarm/test-alarm');
        $alarm->setResourceName('test-resource');
        $alarm->setResourceType('Instance');
        $alarm->setRegion('us-east-1');
        $alarm->setComparisonOperator('>=');
        $alarm->setEvaluationPeriods('1');
        $alarm->setThreshold(80.0);
        $alarm->setMetricName(AlarmMetricEnum::CPU_UTILIZATION);
        $alarm->setState(AlarmStateEnum::ALARM);
        $alarm->setCredential($credential);

        return $alarm;
    }

    protected function getRepository(): AlarmRepository
    {
        $repository = self::getContainer()->get(AlarmRepository::class);
        $this->assertInstanceOf(AlarmRepository::class, $repository);

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
        $this->assertInstanceOf(Alarm::class, $entity);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $foundEntity = $this->getRepository()->find($entity->getId());
        $this->assertNotNull($foundEntity);
        $this->assertInstanceOf(Alarm::class, $foundEntity);
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

        $foundEntity = $this->getRepository()->findOneBy(['name' => 'Test Alarm']);
        $this->assertNotNull($foundEntity);
        $this->assertSame('us-east-1', $foundEntity->getRegion());
    }

    /**
     * 测试按资源名称查找告警
     */
    public function testFindByResourceName(): void
    {
        $entity = $this->createNewEntity();
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByResourceName('test-resource');

        $this->assertGreaterThanOrEqual(1, \count($result));
        $this->assertInstanceOf(Alarm::class, $result[0]);
        $this->assertSame('test-resource', $result[0]->getResourceName());
    }

    /**
     * 测试按资源类型查找告警
     */
    public function testFindByResourceType(): void
    {
        $entity = $this->createNewEntity();
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByResourceType('Instance');

        $this->assertGreaterThanOrEqual(1, \count($result));
        $this->assertInstanceOf(Alarm::class, $result[0]);
        $this->assertSame('Instance', $result[0]->getResourceType());
    }

    /**
     * 测试按状态查找告警
     */
    public function testFindByState(): void
    {
        $entity = $this->createNewEntity();
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByState(AlarmStateEnum::ALARM->value);

        $this->assertGreaterThanOrEqual(1, \count($result));
        $this->assertInstanceOf(Alarm::class, $result[0]);
        $this->assertSame(AlarmStateEnum::ALARM, $result[0]->getState());
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
