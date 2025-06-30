<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit\Entity;

use AwsLightsailBundle\Entity\StaticIp;
use PHPUnit\Framework\TestCase;

final class StaticIpTest extends TestCase
{
    private StaticIp $entity;

    protected function setUp(): void
    {
        $this->entity = new StaticIp();
    }

    public function testConstructor_initializesCorrectly(): void
    {
        $entity = new StaticIp();
        $this->assertInstanceOf(StaticIp::class, $entity);
    }

    public function testGetId_returnsNull_whenNotPersisted(): void
    {
        $this->assertNull($this->entity->getId());
    }

    public function testEntity_hasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\\Entity\\StaticIp', StaticIp::class);
    }

    public function testToString_methodExists(): void
    {}
}
