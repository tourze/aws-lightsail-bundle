<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Entity;

use AwsLightsailBundle\Entity\DomainEntry;
use AwsLightsailBundle\Enum\DnsRecordTypeEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(DomainEntry::class)]
final class DomainEntryTest extends AbstractEntityTestCase
{
    private DomainEntry $entity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entity = new DomainEntry();
    }

    protected function createEntity(): object
    {
        return new DomainEntry();
    }

    public function testConstructorInitializesCorrectly(): void
    {
        $entity = new DomainEntry();
        $this->assertInstanceOf(DomainEntry::class, $entity);
    }

    public function testGetIdReturnsNullWhenNotPersisted(): void
    {
        $this->assertNull($this->entity->getId());
    }

    public function testEntityHasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\Entity\DomainEntry', DomainEntry::class);
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'name'  => ['name', 'www'],
            'type'  => ['type', DnsRecordTypeEnum::A],
            'value' => ['value', '192.168.1.1'],
            'ttl'   => ['ttl', 3600],
            // 注意：isAlias 属性暂时排除，因为它的 setter 方法使用了 setIs 前缀，不符合 AbstractEntityTest 的约定
            'priority' => ['priority', 10],
            'syncTime' => ['syncTime', new \DateTimeImmutable('2023-01-01 12:00:00')],
        ];
    }
}
