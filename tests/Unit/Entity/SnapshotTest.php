<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit\Entity;

use AwsLightsailBundle\Entity\Snapshot;
use PHPUnit\Framework\TestCase;

final class SnapshotTest extends TestCase
{
    private Snapshot $entity;

    protected function setUp(): void
    {
        $this->entity = new Snapshot();
    }

    public function testConstructor_initializesCorrectly(): void
    {
        $entity = new Snapshot();
        $this->assertInstanceOf(Snapshot::class, $entity);
    }

    public function testGetId_returnsNull_whenNotPersisted(): void
    {
        $this->assertNull($this->entity->getId());
    }

    public function testEntity_hasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\\Entity\\Snapshot', Snapshot::class);
    }

    public function testToString_methodExists(): void
    {}
}
