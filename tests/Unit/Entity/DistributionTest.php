<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit\Entity;

use AwsLightsailBundle\Entity\Distribution;
use PHPUnit\Framework\TestCase;

final class DistributionTest extends TestCase
{
    private Distribution $entity;

    protected function setUp(): void
    {
        $this->entity = new Distribution();
    }

    public function testConstructor_initializesCorrectly(): void
    {
        $entity = new Distribution();
        $this->assertInstanceOf(Distribution::class, $entity);
    }

    public function testGetId_returnsNull_whenNotPersisted(): void
    {
        $this->assertNull($this->entity->getId());
    }

    public function testEntity_hasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\\Entity\\Distribution', Distribution::class);
    }

    public function testToString_methodExists(): void
    {}
}
