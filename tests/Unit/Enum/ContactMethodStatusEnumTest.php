<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit\Enum;

use AwsLightsailBundle\Enum\ContactMethodStatusEnum;
use PHPUnit\Framework\TestCase;

final class ContactMethodStatusEnumTest extends TestCase
{
    public function testEnum_isBackedEnum(): void
    {
        $this->assertTrue(enum_exists(ContactMethodStatusEnum::class));
    }

    public function testEnum_hasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\\Enum\\ContactMethodStatusEnum', ContactMethodStatusEnum::class);
    }public function testCases_returnsNonEmptyArray(): void
    {
        $cases = ContactMethodStatusEnum::cases();}
}
