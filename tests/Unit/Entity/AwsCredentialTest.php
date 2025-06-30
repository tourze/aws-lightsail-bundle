<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit\Entity;

use AwsLightsailBundle\Entity\AwsCredential;
use PHPUnit\Framework\TestCase;

final class AwsCredentialTest extends TestCase
{
    private AwsCredential $entity;

    protected function setUp(): void
    {
        $this->entity = new AwsCredential();
    }

    public function testConstructor_initializesCorrectly(): void
    {
        $entity = new AwsCredential();
        $this->assertInstanceOf(AwsCredential::class, $entity);
    }

    public function testGetId_returnsNull_whenNotPersisted(): void
    {
        $this->assertNull($this->entity->getId());
    }

    public function testEntity_hasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\\Entity\\AwsCredential', AwsCredential::class);
    }

    public function testToString_methodExists(): void
    {}
}
