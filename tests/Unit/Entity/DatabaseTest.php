<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit\Entity;

use AwsLightsailBundle\Entity\Database;
use PHPUnit\Framework\TestCase;

final class DatabaseTest extends TestCase
{
    private Database $entity;

    protected function setUp(): void
    {
        $this->entity = new Database();
    }

    public function testConstructor_initializesCorrectly(): void
    {
        $entity = new Database();
        $this->assertInstanceOf(Database::class, $entity);
    }

    public function testGetId_returnsNull_whenNotPersisted(): void
    {
        $this->assertNull($this->entity->getId());
    }

    public function testEntity_hasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\\Entity\\Database', Database::class);
    }

    public function testToString_methodExists(): void
    {}
}
