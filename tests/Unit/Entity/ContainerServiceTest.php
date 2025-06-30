<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit\Entity;

use AwsLightsailBundle\Entity\ContainerService;
use PHPUnit\Framework\TestCase;

final class ContainerServiceTest extends TestCase
{
    private ContainerService $entity;

    protected function setUp(): void
    {
        $this->entity = new ContainerService();
    }

    public function testConstructor_initializesCorrectly(): void
    {
        $entity = new ContainerService();
        $this->assertInstanceOf(ContainerService::class, $entity);
    }

    public function testGetId_returnsNull_whenNotPersisted(): void
    {
        $this->assertNull($this->entity->getId());
    }

    public function testEntity_hasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\\Entity\\ContainerService', ContainerService::class);
    }

    public function testToString_methodExists(): void
    {}
}
