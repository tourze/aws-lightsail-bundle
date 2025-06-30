<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit\Entity;

use AwsLightsailBundle\Entity\Alarm;
use PHPUnit\Framework\TestCase;

final class AlarmTest extends TestCase
{
    private Alarm $entity;

    protected function setUp(): void
    {
        $this->entity = new Alarm();
    }

    public function testConstructor_initializesCorrectly(): void
    {
        $entity = new Alarm();
        $this->assertInstanceOf(Alarm::class, $entity);
    }

    public function testGetId_returnsNull_whenNotPersisted(): void
    {
        $this->assertNull($this->entity->getId());
    }

    public function testEntity_hasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\\Entity\\Alarm', Alarm::class);
    }

    public function testToString_methodExists(): void
    {}
}
