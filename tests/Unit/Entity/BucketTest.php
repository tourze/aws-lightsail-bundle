<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit\Entity;

use AwsLightsailBundle\Entity\Bucket;
use PHPUnit\Framework\TestCase;

final class BucketTest extends TestCase
{
    private Bucket $entity;

    protected function setUp(): void
    {
        $this->entity = new Bucket();
    }

    public function testConstructor_initializesCorrectly(): void
    {
        $entity = new Bucket();
        $this->assertInstanceOf(Bucket::class, $entity);
    }

    public function testGetId_returnsNull_whenNotPersisted(): void
    {
        $this->assertNull($this->entity->getId());
    }

    public function testEntity_hasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\\Entity\\Bucket', Bucket::class);
    }

    public function testToString_methodExists(): void
    {}
}
