<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit\Entity;

use AwsLightsailBundle\Entity\Instance;
use PHPUnit\Framework\TestCase;

final class InstanceTest extends TestCase
{
    private Instance $entity;

    protected function setUp(): void
    {
        $this->entity = new Instance();
    }

    public function testConstructor_initializesCorrectly(): void
    {
        $entity = new Instance();
        $this->assertInstanceOf(Instance::class, $entity);
    }

    public function testGetId_returnsNull_whenNotPersisted(): void
    {
        $this->assertNull($this->entity->getId());
    }

    public function testEntity_hasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\\Entity\\Instance', Instance::class);
    }

    public function testToString_methodExists(): void
    {}
}
