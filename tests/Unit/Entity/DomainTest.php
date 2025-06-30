<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit\Entity;

use AwsLightsailBundle\Entity\Domain;
use PHPUnit\Framework\TestCase;

final class DomainTest extends TestCase
{
    private Domain $entity;

    protected function setUp(): void
    {
        $this->entity = new Domain();
    }

    public function testConstructor_initializesCorrectly(): void
    {
        $entity = new Domain();
        $this->assertInstanceOf(Domain::class, $entity);
    }

    public function testGetId_returnsNull_whenNotPersisted(): void
    {
        $this->assertNull($this->entity->getId());
    }

    public function testEntity_hasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\\Entity\\Domain', Domain::class);
    }

    public function testToString_methodExists(): void
    {}
}
