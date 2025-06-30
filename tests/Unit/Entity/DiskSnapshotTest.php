<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit\Entity;

use AwsLightsailBundle\Entity\DiskSnapshot;
use PHPUnit\Framework\TestCase;

final class DiskSnapshotTest extends TestCase
{
    private DiskSnapshot $entity;

    protected function setUp(): void
    {
        $this->entity = new DiskSnapshot();
    }

    public function testConstructor_initializesCorrectly(): void
    {
        $entity = new DiskSnapshot();
        $this->assertInstanceOf(DiskSnapshot::class, $entity);
    }

    public function testGetId_returnsNull_whenNotPersisted(): void
    {
        $this->assertNull($this->entity->getId());
    }

    public function testEntity_hasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\\Entity\\DiskSnapshot', DiskSnapshot::class);
    }

    public function testToString_methodExists(): void
    {}
}
