<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit\Enum;

use AwsLightsailBundle\Enum\AlarmMetricEnum;
use PHPUnit\Framework\TestCase;

final class AlarmMetricEnumTest extends TestCase
{
    public function testEnum_isBackedEnum(): void
    {
        $this->assertTrue(enum_exists(AlarmMetricEnum::class));
    }

    public function testEnum_hasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\\Enum\\AlarmMetricEnum', AlarmMetricEnum::class);
    }public function testCases_returnsNonEmptyArray(): void
    {
        $cases = AlarmMetricEnum::cases();}
}
