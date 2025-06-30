<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit\Entity;

use AwsLightsailBundle\Entity\LoadBalancer;
use PHPUnit\Framework\TestCase;

final class LoadBalancerTest extends TestCase
{
    private LoadBalancer $entity;

    protected function setUp(): void
    {
        $this->entity = new LoadBalancer();
    }

    public function testConstructor_initializesCorrectly(): void
    {
        $entity = new LoadBalancer();
        $this->assertInstanceOf(LoadBalancer::class, $entity);
    }

    public function testGetId_returnsNull_whenNotPersisted(): void
    {
        $this->assertNull($this->entity->getId());
    }

    public function testEntity_hasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\\Entity\\LoadBalancer', LoadBalancer::class);
    }

    public function testToString_methodExists(): void
    {}
}
