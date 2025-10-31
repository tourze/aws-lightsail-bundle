<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Repository;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Domain;
use AwsLightsailBundle\Entity\DomainEntry;
use AwsLightsailBundle\Enum\DnsRecordTypeEnum;
use AwsLightsailBundle\Repository\DomainEntryRepository;
use Doctrine\DBAL\Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(DomainEntryRepository::class)]
#[RunTestsInSeparateProcesses]
final class DomainEntryRepositoryTest extends AbstractRepositoryTestCase
{
    protected function createNewEntity(): DomainEntry
    {
        $domain = $this->createTestDomain();
        self::getEntityManager()->persist($domain);

        $domainEntry = new DomainEntry();
        $domainEntry->setName('test-entry');
        $domainEntry->setType(DnsRecordTypeEnum::A);
        $domainEntry->setValue('192.168.1.1');
        $domainEntry->setDomain($domain);

        return $domainEntry;
    }

    protected function getRepository(): DomainEntryRepository
    {
        $repository = self::getContainer()->get(DomainEntryRepository::class);
        $this->assertInstanceOf(DomainEntryRepository::class, $repository);

        return $repository;
    }

    protected function onSetUp(): void
    {
        // No additional setup needed
    }

    /**
     * 创建测试用的 Domain 实体
     */
    private function createTestDomain(): Domain
    {
        $credential = $this->createTestAwsCredential();
        self::getEntityManager()->persist($credential);

        $domain = new Domain();
        $domain->setName('test-example.com');
        $domain->setArn('arn:aws:lightsail:us-east-1:123456789012:Domain/test-example.com');
        $domain->setRegion('us-east-1');
        $domain->setCredential($credential);

        return $domain;
    }

    /**
     * 测试查找存在的实体
     */
    public function testFindExistingEntity(): void
    {
        $entity = $this->createNewEntity();
        $this->assertInstanceOf(DomainEntry::class, $entity);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $foundEntity = $this->getRepository()->find($entity->getId());
        $this->assertNotNull($foundEntity);
        $this->assertInstanceOf(DomainEntry::class, $foundEntity);
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

        $foundEntity = $this->getRepository()->findOneBy(['name' => 'test-entry']);
        $this->assertNotNull($foundEntity);
        $this->assertSame(DnsRecordTypeEnum::A, $foundEntity->getType());
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
     * 测试按域名查找域名记录
     */
    public function testFindByDomain(): void
    {
        $entity = $this->createNewEntity();
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByDomain($entity->getDomain());
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(DomainEntry::class, $result[0]);
        $this->assertSame($entity->getDomain()->getId(), $result[0]->getDomain()->getId());
    }

    /**
     * 测试按记录类型查找域名记录
     */
    public function testFindByType(): void
    {
        $entity = $this->createNewEntity();
        $entity->setName('unique-type-test-entry');
        $entity->setType(DnsRecordTypeEnum::CNAME);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByType(DnsRecordTypeEnum::CNAME);
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result));
        $found = false;
        foreach ($result as $entry) {
            $this->assertInstanceOf(DomainEntry::class, $entry);
            if ('unique-type-test-entry' === $entry->getName()) {
                $this->assertSame(DnsRecordTypeEnum::CNAME, $entry->getType());
                $found = true;

                break;
            }
        }
        $this->assertTrue($found, '未找到预期的域名记录实体');
    }

    /**
     * 测试按记录名称查找域名记录
     */
    public function testFindByName(): void
    {
        $entity = $this->createNewEntity();
        $entity->setName('unique-www-test');
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByName('unique-www-test');
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result));
        $found = false;
        foreach ($result as $entry) {
            $this->assertInstanceOf(DomainEntry::class, $entry);
            if ('unique-www-test' === $entry->getName()) {
                $found = true;

                break;
            }
        }
        $this->assertTrue($found, '未找到预期的域名记录实体');
    }

    /**
     * 测试按记录值查找域名记录
     */
    public function testFindByValue(): void
    {
        $entity = $this->createNewEntity();
        $entity->setName('unique-value-test-entry');
        $entity->setValue('192.168.1.100');
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByValue('192.168.1.100');
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result));
        $found = false;
        foreach ($result as $entry) {
            $this->assertInstanceOf(DomainEntry::class, $entry);
            if ('unique-value-test-entry' === $entry->getName()) {
                $this->assertSame('192.168.1.100', $entry->getValue());
                $found = true;

                break;
            }
        }
        $this->assertTrue($found, '未找到预期的域名记录实体');
    }

    /**
     * 测试查找别名记录
     */
    public function testFindAliasRecords(): void
    {
        $entity = $this->createNewEntity();
        $entity->setName('unique-alias-test-entry');
        $entity->setIsAlias(true);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findAliasRecords();
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result));
        $found = false;
        foreach ($result as $entry) {
            $this->assertInstanceOf(DomainEntry::class, $entry);
            if ('unique-alias-test-entry' === $entry->getName()) {
                $this->assertTrue($entry->isAlias());
                $found = true;

                break;
            }
        }
        $this->assertTrue($found, '未找到预期的别名记录实体');
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
