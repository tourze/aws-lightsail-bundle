<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit\Entity;

use AwsLightsailBundle\Entity\Certificate;
use PHPUnit\Framework\TestCase;

final class CertificateTest extends TestCase
{
    private Certificate $entity;

    protected function setUp(): void
    {
        $this->entity = new Certificate();
    }

    public function testConstructor_initializesCorrectly(): void
    {
        $entity = new Certificate();
        $this->assertInstanceOf(Certificate::class, $entity);
    }

    public function testGetId_returnsNull_whenNotPersisted(): void
    {
        $this->assertNull($this->entity->getId());
    }

    public function testEntity_hasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\\Entity\\Certificate', Certificate::class);
    }

    public function testToString_methodExists(): void
    {}
}
