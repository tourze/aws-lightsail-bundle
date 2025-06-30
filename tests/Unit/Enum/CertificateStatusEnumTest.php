<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit\Enum;

use AwsLightsailBundle\Enum\CertificateStatusEnum;
use PHPUnit\Framework\TestCase;

final class CertificateStatusEnumTest extends TestCase
{
    public function testEnum_isBackedEnum(): void
    {
        $this->assertTrue(enum_exists(CertificateStatusEnum::class));
    }

    public function testEnum_hasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\\Enum\\CertificateStatusEnum', CertificateStatusEnum::class);
    }public function testCases_returnsNonEmptyArray(): void
    {
        $cases = CertificateStatusEnum::cases();}
}
