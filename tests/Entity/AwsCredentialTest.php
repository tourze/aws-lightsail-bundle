<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Entity;

use AwsLightsailBundle\Entity\AwsCredential;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(AwsCredential::class)]
final class AwsCredentialTest extends AbstractEntityTestCase
{
    private AwsCredential $entity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entity = new AwsCredential();
    }

    protected function createEntity(): object
    {
        return new AwsCredential();
    }

    public function testConstructorInitializesCorrectly(): void
    {
        $entity = new AwsCredential();
        $this->assertInstanceOf(AwsCredential::class, $entity);
    }

    public function testGetIdReturnsNullWhenNotPersisted(): void
    {
        $this->assertNull($this->entity->getId());
    }

    public function testEntityHasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\Entity\AwsCredential', AwsCredential::class);
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'name'            => ['name', 'test-credential'],
            'accessKeyId'     => ['accessKeyId', 'AKIAIOSFODNN7EXAMPLE'],
            'secretAccessKey' => ['secretAccessKey', 'wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY'],
            // 注意：isDefault 属性暂时排除，因为它的 setter 方法使用了 setIs 前缀，不符合 AbstractEntityTest 的约定
            'region'   => ['region', 'us-east-1'],
            'syncTime' => ['syncTime', new \DateTimeImmutable('2023-01-01 12:00:00')],
        ];
    }
}
