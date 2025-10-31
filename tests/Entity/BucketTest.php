<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Entity;

use AwsLightsailBundle\Entity\Bucket;
use AwsLightsailBundle\Enum\BucketAccessRuleEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Bucket::class)]
final class BucketTest extends AbstractEntityTestCase
{
    private Bucket $entity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entity = new Bucket();
    }

    protected function createEntity(): object
    {
        return new Bucket();
    }

    public function testConstructorInitializesCorrectly(): void
    {
        $entity = new Bucket();
        $this->assertInstanceOf(Bucket::class, $entity);
    }

    public function testGetIdReturnsNullWhenNotPersisted(): void
    {
        $this->assertNull($this->entity->getId());
    }

    public function testEntityHasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\Entity\Bucket', Bucket::class);
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'name'                   => ['name', 'test-bucket'],
            'arn'                    => ['arn', 'arn:aws:lightsail:us-east-1:123456789012:Bucket/test-bucket'],
            'url'                    => ['url', 'https://test-bucket.s3.amazonaws.com'],
            'bundleId'               => ['bundleId', 'small_1_0'],
            'region'                 => ['region', 'us-east-1'],
            'accessRules'            => ['accessRules', BucketAccessRuleEnum::PRIVATE],
            'readonlyAccessAccounts' => ['readonlyAccessAccounts', ['user1@example.com', 'user2@example.com']],
            // 注意：isVersioning 属性暂时排除，因为它的 setter 方法使用了 setIs 前缀，不符合 AbstractEntityTest 的约定
            // 注意：objectVersioning 属性暂时排除，因为它的 setter 方法使用了 set 前缀 + is 前缀的 getter，不符合 AbstractEntityTest 的约定
            // 注意：isResourceType 属性暂时排除，因为它的 setter 方法使用了 setIs 前缀，不符合 AbstractEntityTest 的约定
            'tags'        => ['tags', ['Environment' => 'production', 'Project' => 'test']],
            'sizeInMb'    => ['sizeInMb', 1024],
            'objectCount' => ['objectCount', 100],
            'syncTime'    => ['syncTime', new \DateTimeImmutable('2023-01-01 12:00:00')],
            'corsRules'   => ['corsRules', [['rule' => 'allow-all', 'allowedOrigins' => ['*']]]],
        ];
    }
}
