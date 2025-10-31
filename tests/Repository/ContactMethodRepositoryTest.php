<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Repository;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\ContactMethod;
use AwsLightsailBundle\Enum\ContactMethodStatusEnum;
use AwsLightsailBundle\Enum\ContactMethodTypeEnum;
use AwsLightsailBundle\Repository\ContactMethodRepository;
use Doctrine\DBAL\Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(ContactMethodRepository::class)]
#[RunTestsInSeparateProcesses]
final class ContactMethodRepositoryTest extends AbstractRepositoryTestCase
{
    protected function createNewEntity(): ContactMethod
    {
        $contactMethod = new ContactMethod();
        $contactMethod->setName('Test Contact');
        $contactMethod->setArn('arn:aws:lightsail:us-east-1:123456789012:ContactMethod/test-contact');
        $contactMethod->setType(ContactMethodTypeEnum::EMAIL);
        $contactMethod->setContactEndpoint('test@example.com');
        $contactMethod->setProtocol('email');
        $contactMethod->setStatus(ContactMethodStatusEnum::VERIFIED);
        $contactMethod->setRegion('us-east-1');
        $contactMethod->setCredential($this->createTestAwsCredential());

        return $contactMethod;
    }

    protected function getRepository(): ContactMethodRepository
    {
        $repository = self::getContainer()->get(ContactMethodRepository::class);
        $this->assertInstanceOf(ContactMethodRepository::class, $repository);

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
        $this->assertInstanceOf(ContactMethod::class, $entity);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $foundEntity = $this->getRepository()->find($entity->getId());
        $this->assertNotNull($foundEntity);
        $this->assertInstanceOf(ContactMethod::class, $foundEntity);
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

        $foundEntity = $this->getRepository()->findOneBy(['name' => 'Test Contact']);
        $this->assertNotNull($foundEntity);
        $this->assertInstanceOf(ContactMethod::class, $foundEntity);
        $this->assertSame('test@example.com', $foundEntity->getContactEndpoint());
    }

    /**
     * 测试按类型查找联系方式
     */
    public function testFindByType(): void
    {
        $entity = $this->createNewEntity();
        $entity->setType(ContactMethodTypeEnum::SMS);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByType(ContactMethodTypeEnum::SMS);
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result), 'Should find at least our inserted ContactMethod');

        // 验证所有返回结果都是正确类型
        foreach ($result as $contactMethod) {
            $this->assertInstanceOf(ContactMethod::class, $contactMethod);
            $this->assertSame(ContactMethodTypeEnum::SMS, $contactMethod->getType());
        }

        // 验证我们插入的实体在结果中
        $found = false;
        foreach ($result as $contactMethod) {
            if ($contactMethod->getName() === $entity->getName() && $contactMethod->getArn() === $entity->getArn()) {
                $found = true;

                break;
            }
        }
        $this->assertTrue($found, 'Our inserted ContactMethod should be in the results');
    }

    /**
     * 测试按状态查找联系方式
     */
    public function testFindByStatus(): void
    {
        $entity = $this->createNewEntity();
        $entity->setStatus(ContactMethodStatusEnum::PENDING);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByStatus(ContactMethodStatusEnum::PENDING);
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result), 'Should find at least our inserted ContactMethod');

        // 验证所有返回结果都是正确状态
        foreach ($result as $contactMethod) {
            $this->assertInstanceOf(ContactMethod::class, $contactMethod);
            $this->assertSame(ContactMethodStatusEnum::PENDING, $contactMethod->getStatus());
        }

        // 验证我们插入的实体在结果中
        $found = false;
        foreach ($result as $contactMethod) {
            if ($contactMethod->getName() === $entity->getName() && $contactMethod->getArn() === $entity->getArn()) {
                $found = true;

                break;
            }
        }
        $this->assertTrue($found, 'Our inserted ContactMethod should be in the results');
    }

    /**
     * 测试按联系端点查找联系方式
     */
    public function testFindByContactEndpoint(): void
    {
        $entity = $this->createNewEntity();
        $entity->setContactEndpoint('unique@example.com');
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $result = $this->getRepository()->findByContactEndpoint('unique@example.com');
        $this->assertInstanceOf(ContactMethod::class, $result);
        $this->assertSame('unique@example.com', $result->getContactEndpoint());
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
