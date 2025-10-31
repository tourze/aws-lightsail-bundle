<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Entity;

use AwsLightsailBundle\Entity\Domain;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Domain::class)]
final class DomainTest extends AbstractEntityTestCase
{
    private Domain $entity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entity = new Domain();
    }

    protected function createEntity(): object
    {
        return new Domain();
    }

    public function testConstructorInitializesCorrectly(): void
    {
        $entity = new Domain();
        $this->assertInstanceOf(Domain::class, $entity);
    }

    public function testGetIdReturnsNullWhenNotPersisted(): void
    {
        $this->assertNull($this->entity->getId());
    }

    public function testEntityHasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\Entity\Domain', Domain::class);
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'name'   => ['name', 'example.com'],
            'arn'    => ['arn', 'arn:aws:lightsail:us-east-1:123456789012:Domain/example.com'],
            'region' => ['region', 'us-east-1'],
            // 注意：isManaged 属性暂时排除，因为它的 setter 方法使用了 setIs 前缀，不符合 AbstractEntityTest 的约定
            'tags'     => ['tags', ['Environment' => 'production', 'Project' => 'test']],
            'syncTime' => ['syncTime', new \DateTimeImmutable('2023-01-01 12:00:00')],
        ];
    }
}
