<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit\Enum;

use AwsLightsailBundle\Enum\DistributionStatusEnum;
use PHPUnit\Framework\TestCase;

final class DistributionStatusEnumTest extends TestCase
{
    public function testEnum_isBackedEnum(): void
    {
        $this->assertTrue(enum_exists(DistributionStatusEnum::class));
    }

    public function testEnum_hasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\\Enum\\DistributionStatusEnum', DistributionStatusEnum::class);
    }public function testCases_returnsNonEmptyArray(): void
    {
        $cases = DistributionStatusEnum::cases();}
}
