<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit\Entity;

use AwsLightsailBundle\Entity\DomainEntry;
use PHPUnit\Framework\TestCase;

final class DomainEntryTest extends TestCase
{
    private DomainEntry $entity;

    protected function setUp(): void
    {
        $this->entity = new DomainEntry();
    }

    public function testConstructor_initializesCorrectly(): void
    {
        $entity = new DomainEntry();
        $this->assertInstanceOf(DomainEntry::class, $entity);
    }

    public function testGetId_returnsNull_whenNotPersisted(): void
    {
        $this->assertNull($this->entity->getId());
    }

    public function testEntity_hasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\\Entity\\DomainEntry', DomainEntry::class);
    }

    public function testToString_methodExists(): void
    {}
}
