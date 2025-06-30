<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit\Entity;

use AwsLightsailBundle\Entity\Disk;
use PHPUnit\Framework\TestCase;

final class DiskTest extends TestCase
{
    private Disk $entity;

    protected function setUp(): void
    {
        $this->entity = new Disk();
    }

    public function testConstructor_initializesCorrectly(): void
    {
        $entity = new Disk();
        $this->assertInstanceOf(Disk::class, $entity);
    }

    public function testGetId_returnsNull_whenNotPersisted(): void
    {
        $this->assertNull($this->entity->getId());
    }

    public function testEntity_hasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\\Entity\\Disk', Disk::class);
    }

    public function testToString_methodExists(): void
    {}
}
