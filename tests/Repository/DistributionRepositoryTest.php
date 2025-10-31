<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Repository;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Distribution;
use AwsLightsailBundle\Enum\DistributionStatusEnum;
use AwsLightsailBundle\Repository\DistributionRepository;
use Doctrine\DBAL\Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(DistributionRepository::class)]
#[RunTestsInSeparateProcesses]
final class DistributionRepositoryTest extends AbstractRepositoryTestCase
{
    protected function createNewEntity(): Distribution
    {
        $distribution = new Distribution();
        $distribution->setName('test-distribution');
        $distribution->setArn('arn:aws:lightsail:us-east-1:123456789012:Distribution/test-distribution');
        $distribution->setRegion('us-east-1');
        $distribution->setDefaultDomainName('test.example.com');
        $distribution->setOriginConfigs([
            'test-origin' => [
                'originName' => 'test-origin',
                'originPath' => '/path',
                'domainName' => 'test.example.com',
            ],
        ]);
        $distribution->setCredential($this->createTestAwsCredential());

        return $distribution;
    }

    protected function getRepository(): DistributionRepository
    {
        $repository = self::getContainer()->get(DistributionRepository::class);
        $this->assertInstanceOf(DistributionRepository::class, $repository);

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
        $this->assertInstanceOf(Distribution::class, $entity);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $foundEntity = $this->getRepository()->find($entity->getId());
        $this->assertNotNull($foundEntity);
        $this->assertInstanceOf(Distribution::class, $foundEntity);
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

        $foundEntity = $this->getRepository()->findOneBy(['name' => 'test-distribution']);
        $this->assertNotNull($foundEntity);
        $this->assertSame('us-east-1', $foundEntity->getRegion());
    }

    /**
     * 测试按状态查找分发
     */
    public function testFindByStatus(): void
    {
        $entity = $this->createNewEntity();
        $entity->setName('unique-status-test-distribution');
        $entity->setStatus(DistributionStatusEnum::ACTIVE);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByStatus(DistributionStatusEnum::ACTIVE);
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result));
        $found = false;
        foreach ($result as $distribution) {
            $this->assertInstanceOf(Distribution::class, $distribution);
            if ('unique-status-test-distribution' === $distribution->getName()) {
                $this->assertSame(DistributionStatusEnum::ACTIVE, $distribution->getStatus());
                $found = true;

                break;
            }
        }
        $this->assertTrue($found, '未找到预期的分发实体');
    }

    /**
     * 测试按区域查找分发
     */
    public function testFindByRegion(): void
    {
        $entity = $this->createNewEntity();
        $entity->setName('unique-region-test-distribution');
        $entity->setRegion('us-west-2');
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByRegion('us-west-2');
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result));
        $found = false;
        foreach ($result as $distribution) {
            $this->assertInstanceOf(Distribution::class, $distribution);
            if ('unique-region-test-distribution' === $distribution->getName()) {
                $this->assertSame('us-west-2', $distribution->getRegion());
                $found = true;

                break;
            }
        }
        $this->assertTrue($found, '未找到预期的分发实体');
    }

    /**
     * 测试查找已启用的分发
     */
    public function testFindEnabled(): void
    {
        $entity = $this->createNewEntity();
        $entity->setName('unique-enabled-test-distribution');
        $entity->setIsEnabled(true);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findEnabled();
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result));
        $found = false;
        foreach ($result as $distribution) {
            $this->assertInstanceOf(Distribution::class, $distribution);
            if ('unique-enabled-test-distribution' === $distribution->getName()) {
                $this->assertTrue($distribution->isEnabled());
                $found = true;

                break;
            }
        }
        $this->assertTrue($found, '未找到预期的分发实体');
    }

    /**
     * 测试按证书名称查找分发
     */
    public function testFindByCertificate(): void
    {
        $entity = $this->createNewEntity();
        $entity->setName('unique-cert-test-distribution');
        $entity->setCertificateName('test-certificate-unique');
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByCertificate('test-certificate-unique');
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result));
        $found = false;
        foreach ($result as $distribution) {
            $this->assertInstanceOf(Distribution::class, $distribution);
            if ('unique-cert-test-distribution' === $distribution->getName()) {
                $this->assertSame('test-certificate-unique', $distribution->getCertificateName());
                $found = true;

                break;
            }
        }
        $this->assertTrue($found, '未找到预期的分发实体');
    }

    /**
     * 测试按域名查找分发
     */
    public function testFindByDomainName(): void
    {
        $entity = $this->createNewEntity();
        $entity->setName('unique-domain-test-distribution');
        $entity->setDefaultDomainName('unique-example.com');
        $entity->setAlternativeDomainNames(['www.unique-example.com', 'cdn.unique-example.com']);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        // 测试默认域名匹配
        $result = $this->getRepository()->findByDomainName('unique-example.com');
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result));
        $foundDefault = false;
        foreach ($result as $distribution) {
            $this->assertInstanceOf(Distribution::class, $distribution);
            if ('unique-domain-test-distribution' === $distribution->getName()) {
                $foundDefault = true;

                break;
            }
        }
        $this->assertTrue($foundDefault, '未找到默认域名匹配的分发实体');

        // 测试备用域名匹配
        $result = $this->getRepository()->findByDomainName('www.unique-example.com');
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result));
        $foundAlt = false;
        foreach ($result as $distribution) {
            $this->assertInstanceOf(Distribution::class, $distribution);
            if ('unique-domain-test-distribution' === $distribution->getName()) {
                $foundAlt = true;

                break;
            }
        }
        $this->assertTrue($foundAlt, '未找到备用域名匹配的分发实体');
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
