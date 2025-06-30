<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit\Entity;

use AwsLightsailBundle\Entity\ContactMethod;
use PHPUnit\Framework\TestCase;

final class ContactMethodTest extends TestCase
{
    private ContactMethod $entity;

    protected function setUp(): void
    {
        $this->entity = new ContactMethod();
    }

    public function testConstructor_initializesCorrectly(): void
    {
        $entity = new ContactMethod();
        $this->assertInstanceOf(ContactMethod::class, $entity);
    }

    public function testGetId_returnsNull_whenNotPersisted(): void
    {
        $this->assertNull($this->entity->getId());
    }

    public function testEntity_hasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\\Entity\\ContactMethod', ContactMethod::class);
    }

    public function testToString_methodExists(): void
    {}
}
