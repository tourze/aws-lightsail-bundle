<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Repository;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Certificate;
use AwsLightsailBundle\Enum\CertificateStatusEnum;
use AwsLightsailBundle\Repository\CertificateRepository;
use Doctrine\DBAL\Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(CertificateRepository::class)]
#[RunTestsInSeparateProcesses]
final class CertificateRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 初始化逻辑
    }

    protected function createNewEntity(): Certificate
    {
        $certificate = new Certificate();
        $certificate->setName('test-certificate');
        $certificate->setArn('arn:aws:lightsail:us-east-1:123456789012:Certificate/test-certificate');
        $certificate->setDomainName('test-example.com');
        $certificate->setSubjectAlternativeNames(['www.test-example.com']);
        $certificate->setRegion('us-east-1');
        $certificate->setCredential($this->createTestAwsCredential());

        return $certificate;
    }

    protected function getRepository(): CertificateRepository
    {
        $repository = self::getContainer()->get(CertificateRepository::class);
        $this->assertInstanceOf(CertificateRepository::class, $repository);

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
        $this->assertSame('test-certificate', $foundEntity->getName());
    }

    /**
     * 测试保存和检索实体
     */
    public function testSaveAndRetrieveEntity(): void
    {
        $entity = $this->createNewEntity();
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $foundEntity = $this->getRepository()->findOneBy(['name' => 'test-certificate']);
        $this->assertNotNull($foundEntity);
        $this->assertSame('us-east-1', $foundEntity->getRegion());
    }

    /**
     * 测试按域名查找证书
     */
    public function testFindByDomainName(): void
    {
        $entity = $this->createNewEntity();
        $entity->setDomainName('example.com');
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByDomainName('example.com');
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result), 'Should find at least our inserted Certificate');

        // 验证所有返回结果都是正确域名
        foreach ($result as $certificate) {
            $this->assertInstanceOf(Certificate::class, $certificate);
            $this->assertSame('example.com', $certificate->getDomainName());
        }

        // 验证我们插入的实体在结果中
        $found = false;
        foreach ($result as $certificate) {
            if ($certificate->getName() === $entity->getName() && $certificate->getArn() === $entity->getArn()) {
                $found = true;

                break;
            }
        }
        $this->assertTrue($found, 'Our inserted Certificate should be in the results');
    }

    /**
     * 测试按状态查找证书
     */
    public function testFindByStatus(): void
    {
        $entity = $this->createNewEntity();
        $entity->setStatus(CertificateStatusEnum::ISSUED);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByStatus(CertificateStatusEnum::ISSUED);
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result), 'Should find at least our inserted Certificate');

        // 验证所有返回结果都是正确状态
        foreach ($result as $certificate) {
            $this->assertInstanceOf(Certificate::class, $certificate);
            $this->assertSame(CertificateStatusEnum::ISSUED, $certificate->getStatus());
        }

        // 验证我们插入的实体在结果中
        $found = false;
        foreach ($result as $certificate) {
            if ($certificate->getName() === $entity->getName() && $certificate->getArn() === $entity->getArn()) {
                $found = true;

                break;
            }
        }
        $this->assertTrue($found, 'Our inserted Certificate should be in the results');
    }

    /**
     * 测试查找即将过期的证书
     */
    public function testFindExpiringCertificates(): void
    {
        $entity         = $this->createNewEntity();
        $expirationDate = new \DateTime('+15 days');
        $entity->setValidToTime($expirationDate);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findExpiringCertificates(30);
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result), 'Should find at least our inserted Certificate');

        // 验证所有返回结果都有过期时间且在范围内
        $now             = new \DateTime();
        $thirtyDaysLater = new \DateTime('+30 days');
        foreach ($result as $certificate) {
            $this->assertInstanceOf(Certificate::class, $certificate);
            $this->assertNotNull($certificate->getValidToTime());
            $this->assertGreaterThanOrEqual($now, $certificate->getValidToTime());
            $this->assertLessThanOrEqual($thirtyDaysLater, $certificate->getValidToTime());
        }

        // 验证我们插入的实体在结果中
        $found = false;
        foreach ($result as $certificate) {
            if ($certificate->getName() === $entity->getName() && $certificate->getArn() === $entity->getArn()) {
                $found = true;

                break;
            }
        }
        $this->assertTrue($found, 'Our inserted Certificate should be in the results');
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
