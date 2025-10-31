<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Repository;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Domain;
use AwsLightsailBundle\Repository\DomainRepository;
use Doctrine\DBAL\Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(DomainRepository::class)]
#[RunTestsInSeparateProcesses]
final class DomainRepositoryTest extends AbstractRepositoryTestCase
{
    protected function createNewEntity(): Domain
    {
        $domain = new Domain();
        $domain->setName('test-example.com');
        $domain->setArn('arn:aws:lightsail:us-east-1:123456789012:Domain/test-example.com');
        $domain->setRegion('us-east-1');
        $domain->setCredential($this->createTestAwsCredential());

        return $domain;
    }

    protected function getRepository(): DomainRepository
    {
        $repository = self::getContainer()->get(DomainRepository::class);
        $this->assertInstanceOf(DomainRepository::class, $repository);

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
        $this->assertInstanceOf(Domain::class, $entity);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $foundEntity = $this->getRepository()->find($entity->getId());
        $this->assertNotNull($foundEntity);
        $this->assertInstanceOf(Domain::class, $foundEntity);
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

        $foundEntity = $this->getRepository()->findOneBy(['name' => 'test-example.com']);
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
     * 测试按名称模式查找域名
     */
    public function testFindByNamePattern(): void
    {
        $uniqueId      = \uniqid('test-pattern-', true);
        $uniquePattern = 'unique' . \substr($uniqueId, -8);

        // 创建包含唯一模式的域名
        $domain1 = $this->createNewEntity();
        $domain1->setName('test-' . $uniquePattern . '.com');
        self::getEntityManager()->persist($domain1);

        // 创建另一个包含唯一模式的域名
        $domain2 = new Domain();
        $domain2->setName($uniquePattern . '-site.com');
        $domain2->setArn('arn:aws:lightsail:us-east-1:123456789012:Domain/' . $uniquePattern . '-site.com');
        $domain2->setRegion('us-east-1');
        $domain2->setCredential($this->createTestAwsCredential());
        self::getEntityManager()->persist($domain2);

        // 创建另一个包含唯一模式的域名
        $domain3 = new Domain();
        $domain3->setName('my' . $uniquePattern . '.org');
        $domain3->setArn('arn:aws:lightsail:us-east-1:123456789012:Domain/my' . $uniquePattern . '.org');
        $domain3->setRegion('us-east-1');
        $domain3->setCredential($this->createTestAwsCredential());
        self::getEntityManager()->persist($domain3);

        // 创建不包含唯一模式的域名
        $domain4 = new Domain();
        $domain4->setName('other-site-' . $uniqueId . '.com');
        $domain4->setArn('arn:aws:lightsail:us-east-1:123456789012:Domain/other-site-' . $uniqueId . '.com');
        $domain4->setRegion('us-east-1');
        $domain4->setCredential($this->createTestAwsCredential());
        self::getEntityManager()->persist($domain4);

        self::getEntityManager()->flush();

        // 测试查找包含唯一模式的域名
        $patternDomains = $this->getRepository()->findByNamePattern($uniquePattern);
        $this->assertGreaterThanOrEqual(3, \count($patternDomains));
        $this->assertContainsOnlyInstancesOf(Domain::class, $patternDomains);
        $patternNames = \array_map(fn ($d) => $d->getName(), $patternDomains);
        $this->assertContains('test-' . $uniquePattern . '.com', $patternNames);
        $this->assertContains($uniquePattern . '-site.com', $patternNames);
        $this->assertContains('my' . $uniquePattern . '.org', $patternNames);
        // 验证不包含唯一模式的域名不在结果中
        foreach ($patternDomains as $domain) {
            $this->assertStringContainsString($uniquePattern, $domain->getName());
        }

        // 测试查找包含 'site' 的域名（应该包含我们创建的至少2个）
        $siteDomains = $this->getRepository()->findByNamePattern('site');
        $siteNames   = \array_map(fn ($d) => $d->getName(), $siteDomains);
        $this->assertContains($uniquePattern . '-site.com', $siteNames);
        $this->assertContains('other-site-' . $uniqueId . '.com', $siteNames);

        // 测试查找不存在的模式
        $nonexistentPattern = 'nonexistent-' . $uniqueId;
        $emptyResult        = $this->getRepository()->findByNamePattern($nonexistentPattern);
        $this->assertCount(0, $emptyResult);
    }

    /**
     * 测试按区域查找域名
     */
    public function testFindByRegion(): void
    {
        $uniqueId = \uniqid('test-region-', true);

        // 创建 us-east-1 区域域名
        $eastDomain1 = $this->createNewEntity();
        $eastDomain1->setName('east-domain-1-' . $uniqueId . '.com');
        $eastDomain1->setRegion('us-east-1');
        self::getEntityManager()->persist($eastDomain1);

        // 创建另一个 us-east-1 区域域名
        $eastDomain2 = new Domain();
        $eastDomain2->setName('east-domain-2-' . $uniqueId . '.com');
        $eastDomain2->setArn('arn:aws:lightsail:us-east-1:123456789012:Domain/east-domain-2-' . $uniqueId . '.com');
        $eastDomain2->setRegion('us-east-1');
        $eastDomain2->setCredential($this->createTestAwsCredential());
        self::getEntityManager()->persist($eastDomain2);

        // 创建 us-west-2 区域域名
        $westDomain = new Domain();
        $westDomain->setName('west-domain-1-' . $uniqueId . '.com');
        $westDomain->setArn('arn:aws:lightsail:us-west-2:123456789012:Domain/west-domain-1-' . $uniqueId . '.com');
        $westDomain->setRegion('us-west-2');
        $credential = new AwsCredential();
        $credential->setName('west-credential-' . $uniqueId);
        $credential->setAccessKeyId('test-access-key');
        $credential->setSecretAccessKey('test-secret-key');
        $credential->setRegion('us-west-2');
        $westDomain->setCredential($credential);
        self::getEntityManager()->persist($westDomain);

        // 创建 eu-central-1 区域域名
        $euDomain = new Domain();
        $euDomain->setName('eu-domain-1-' . $uniqueId . '.com');
        $euDomain->setArn('arn:aws:lightsail:eu-central-1:123456789012:Domain/eu-domain-1-' . $uniqueId . '.com');
        $euDomain->setRegion('eu-central-1');
        $euCredential = new AwsCredential();
        $euCredential->setName('eu-credential-' . $uniqueId);
        $euCredential->setAccessKeyId('test-access-key');
        $euCredential->setSecretAccessKey('test-secret-key');
        $euCredential->setRegion('eu-central-1');
        $euDomain->setCredential($euCredential);
        self::getEntityManager()->persist($euDomain);

        self::getEntityManager()->flush();

        // 测试查找 us-east-1 区域的域名（只验证我们创建的）
        $eastDomains = $this->getRepository()->findByRegion('us-east-1');
        $this->assertGreaterThanOrEqual(2, \count($eastDomains));
        $this->assertContainsOnlyInstancesOf(Domain::class, $eastDomains);
        $eastNames = \array_map(fn ($d) => $d->getName(), $eastDomains);
        $this->assertContains('east-domain-1-' . $uniqueId . '.com', $eastNames);
        $this->assertContains('east-domain-2-' . $uniqueId . '.com', $eastNames);

        // 测试查找 us-west-2 区域的域名（只验证我们创建的）
        $westDomains = $this->getRepository()->findByRegion('us-west-2');
        $this->assertGreaterThanOrEqual(1, \count($westDomains));
        $westNames = \array_map(fn ($d) => $d->getName(), $westDomains);
        $this->assertContains('west-domain-1-' . $uniqueId . '.com', $westNames);

        // 测试查找不存在的区域（使用唯一的区域名）
        $nonexistentRegion = 'test-nonexistent-' . $uniqueId;
        $emptyResult       = $this->getRepository()->findByRegion($nonexistentRegion);
        $this->assertCount(0, $emptyResult);
    }

    /**
     * 测试查找托管的域名
     */
    public function testFindManaged(): void
    {
        $uniqueId = \uniqid('test-managed-', true);

        // 创建托管域名
        $managedDomain1 = $this->createNewEntity();
        $managedDomain1->setName('managed-domain-1-' . $uniqueId . '.com');
        $managedDomain1->setIsManaged(true);
        self::getEntityManager()->persist($managedDomain1);

        // 创建另一个托管域名
        $managedDomain2 = new Domain();
        $managedDomain2->setName('managed-domain-2-' . $uniqueId . '.com');
        $managedDomain2->setArn('arn:aws:lightsail:us-east-1:123456789012:Domain/managed-domain-2-' . $uniqueId . '.com');
        $managedDomain2->setRegion('us-east-1');
        $managedDomain2->setIsManaged(true);
        $managedDomain2->setCredential($this->createTestAwsCredential());
        self::getEntityManager()->persist($managedDomain2);

        // 创建非托管域名
        $unmanagedDomain1 = new Domain();
        $unmanagedDomain1->setName('unmanaged-domain-1-' . $uniqueId . '.com');
        $unmanagedDomain1->setArn('arn:aws:lightsail:us-east-1:123456789012:Domain/unmanaged-domain-1-' . $uniqueId . '.com');
        $unmanagedDomain1->setRegion('us-east-1');
        $unmanagedDomain1->setIsManaged(false);
        $unmanagedDomain1->setCredential($this->createTestAwsCredential());
        self::getEntityManager()->persist($unmanagedDomain1);

        // 创建另一个非托管域名
        $unmanagedDomain2 = new Domain();
        $unmanagedDomain2->setName('unmanaged-domain-2-' . $uniqueId . '.com');
        $unmanagedDomain2->setArn('arn:aws:lightsail:us-east-1:123456789012:Domain/unmanaged-domain-2-' . $uniqueId . '.com');
        $unmanagedDomain2->setRegion('us-east-1');
        $unmanagedDomain2->setIsManaged(false);
        $unmanagedDomain2->setCredential($this->createTestAwsCredential());
        self::getEntityManager()->persist($unmanagedDomain2);

        self::getEntityManager()->flush();

        // 测试查找托管域名（只验证我们创建的）
        $managedDomains = $this->getRepository()->findManaged();
        $this->assertGreaterThanOrEqual(2, \count($managedDomains));
        $this->assertContainsOnlyInstancesOf(Domain::class, $managedDomains);
        $managedNames = \array_map(fn ($d) => $d->getName(), $managedDomains);
        $this->assertContains('managed-domain-1-' . $uniqueId . '.com', $managedNames);
        $this->assertContains('managed-domain-2-' . $uniqueId . '.com', $managedNames);
        // 验证所有域名都是托管的
        foreach ($managedDomains as $domain) {
            $this->assertTrue($domain->isManaged());
        }
    }
}
