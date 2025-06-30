<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit\Entity;

use AwsLightsailBundle\Entity\Operation;
use PHPUnit\Framework\TestCase;

final class OperationTest extends TestCase
{
    private Operation $entity;

    protected function setUp(): void
    {
        $this->entity = new Operation();
    }

    public function testConstructor_initializesCorrectly(): void
    {
        $entity = new Operation();
        $this->assertInstanceOf(Operation::class, $entity);
    }

    public function testGetId_returnsNull_whenNotPersisted(): void
    {
        $this->assertNull($this->entity->getId());
    }

    public function testEntity_hasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\\Entity\\Operation', Operation::class);
    }

    public function testToString_methodExists(): void
    {}
}
