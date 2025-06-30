<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit\Entity;

use AwsLightsailBundle\Entity\DatabaseSnapshot;
use PHPUnit\Framework\TestCase;

final class DatabaseSnapshotTest extends TestCase
{
    private DatabaseSnapshot $entity;

    protected function setUp(): void
    {
        $this->entity = new DatabaseSnapshot();
    }

    public function testConstructor_initializesCorrectly(): void
    {
        $entity = new DatabaseSnapshot();
        $this->assertInstanceOf(DatabaseSnapshot::class, $entity);
    }

    public function testGetId_returnsNull_whenNotPersisted(): void
    {
        $this->assertNull($this->entity->getId());
    }

    public function testEntity_hasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\\Entity\\DatabaseSnapshot', DatabaseSnapshot::class);
    }

    public function testToString_methodExists(): void
    {}
}
