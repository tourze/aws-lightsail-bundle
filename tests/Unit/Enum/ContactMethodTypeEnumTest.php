<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit\Enum;

use AwsLightsailBundle\Enum\ContactMethodTypeEnum;
use PHPUnit\Framework\TestCase;

final class ContactMethodTypeEnumTest extends TestCase
{
    public function testEnum_isBackedEnum(): void
    {
        $this->assertTrue(enum_exists(ContactMethodTypeEnum::class));
    }

    public function testEnum_hasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\\Enum\\ContactMethodTypeEnum', ContactMethodTypeEnum::class);
    }public function testCases_returnsNonEmptyArray(): void
    {
        $cases = ContactMethodTypeEnum::cases();}
}
