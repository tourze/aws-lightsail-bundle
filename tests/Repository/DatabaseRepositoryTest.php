<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Repository;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Database;
use AwsLightsailBundle\Enum\DatabaseEngineEnum;
use AwsLightsailBundle\Enum\DatabaseStatusEnum;
use AwsLightsailBundle\Repository\DatabaseRepository;
use Doctrine\DBAL\Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(DatabaseRepository::class)]
#[RunTestsInSeparateProcesses]
final class DatabaseRepositoryTest extends AbstractRepositoryTestCase
{
    protected function createNewEntity(): Database
    {
        $database = new Database();
        $database->setName('test-database');
        $database->setArn('arn:aws:lightsail:us-east-1:123456789012:Database/test-database');
        $database->setRegion('us-east-1');
        $database->setEngine(DatabaseEngineEnum::MYSQL);
        $database->setEngineVersion('8.0');
        $database->setMasterUsername('root');
        $database->setMasterEndpoint('test-db.example.com');
        $database->setMasterPort(3306);
        $database->setPreferredBackupWindow('02:00-03:00');
        $database->setPreferredMaintenanceWindow('sun:03:00-sun:04:00');
        $database->setBundleId('micro_1_0');
        $database->setCredential($this->createTestAwsCredential());

        return $database;
    }

    protected function getRepository(): DatabaseRepository
    {
        $repository = self::getContainer()->get(DatabaseRepository::class);
        $this->assertInstanceOf(DatabaseRepository::class, $repository);

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
        $this->assertInstanceOf(Database::class, $entity);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $foundEntity = $this->getRepository()->find($entity->getId());
        $this->assertNotNull($foundEntity);
        $this->assertInstanceOf(Database::class, $foundEntity);
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

        $foundEntity = $this->getRepository()->findOneBy(['name' => 'test-database']);
        $this->assertNotNull($foundEntity);
        $this->assertSame('us-east-1', $foundEntity->getRegion());
    }

    /**
     * 测试按引擎查找数据库
     */
    public function testFindByEngine(): void
    {
        $entity = $this->createNewEntity();
        $entity->setEngine(DatabaseEngineEnum::POSTGRES);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByEngine(DatabaseEngineEnum::POSTGRES);
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result), 'Should find at least our inserted database');

        // 验证所有返回的数据库都使用正确的引擎
        foreach ($result as $database) {
            $this->assertInstanceOf(Database::class, $database);
            $this->assertSame(DatabaseEngineEnum::POSTGRES, $database->getEngine());
        }

        // 验证我们插入的数据库在结果中
        $foundOurDatabase = false;
        foreach ($result as $database) {
            if ($database->getName() === $entity->getName() && $database->getArn() === $entity->getArn()) {
                $foundOurDatabase = true;

                break;
            }
        }
        $this->assertTrue($foundOurDatabase, 'Our inserted database should be found in the results');
    }

    /**
     * 测试按状态查找数据库
     */
    public function testFindByStatus(): void
    {
        $entity = $this->createNewEntity();
        $entity->setStatus(DatabaseStatusEnum::AVAILABLE);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByStatus(DatabaseStatusEnum::AVAILABLE);
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result), 'Should find at least our inserted database');

        // 验证所有返回的数据库都有正确的状态
        foreach ($result as $database) {
            $this->assertInstanceOf(Database::class, $database);
            $this->assertSame(DatabaseStatusEnum::AVAILABLE, $database->getStatus());
        }

        // 验证我们插入的数据库在结果中
        $foundOurDatabase = false;
        foreach ($result as $database) {
            if ($database->getName() === $entity->getName() && $database->getArn() === $entity->getArn()) {
                $foundOurDatabase = true;

                break;
            }
        }
        $this->assertTrue($foundOurDatabase, 'Our inserted database should be found in the results');
    }

    /**
     * 测试按区域查找数据库
     */
    public function testFindByRegion(): void
    {
        $entity = $this->createNewEntity();
        $entity->setRegion('eu-west-1');
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByRegion('eu-west-1');
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result), 'Should find at least our inserted database');

        // 验证所有返回的数据库都属于正确的区域
        foreach ($result as $database) {
            $this->assertInstanceOf(Database::class, $database);
            $this->assertSame('eu-west-1', $database->getRegion());
        }

        // 验证我们插入的数据库在结果中
        $foundOurDatabase = false;
        foreach ($result as $database) {
            if ($database->getName() === $entity->getName() && $database->getArn() === $entity->getArn()) {
                $foundOurDatabase = true;

                break;
            }
        }
        $this->assertTrue($foundOurDatabase, 'Our inserted database should be found in the results');
    }

    /**
     * 测试查找公开访问的数据库
     */
    public function testFindPubliclyAccessible(): void
    {
        $entity = $this->createNewEntity();
        $entity->setPubliclyAccessible(true);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findPubliclyAccessible();
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result), 'Should find at least our inserted database');

        // 验证所有返回的数据库都是公开访问的
        foreach ($result as $database) {
            $this->assertInstanceOf(Database::class, $database);
            $this->assertTrue($database->isPubliclyAccessible());
        }

        // 验证我们插入的数据库在结果中
        $foundOurDatabase = false;
        foreach ($result as $database) {
            if ($database->getName() === $entity->getName() && $database->getArn() === $entity->getArn()) {
                $foundOurDatabase = true;

                break;
            }
        }
        $this->assertTrue($foundOurDatabase, 'Our inserted database should be found in the results');
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
