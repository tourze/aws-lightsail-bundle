<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit\Enum;

use AwsLightsailBundle\Enum\AlarmStateEnum;
use PHPUnit\Framework\TestCase;

final class AlarmStateEnumTest extends TestCase
{
    public function testEnum_isBackedEnum(): void
    {
        $this->assertTrue(enum_exists(AlarmStateEnum::class));
    }

    public function testEnum_hasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\\Enum\\AlarmStateEnum', AlarmStateEnum::class);
    }public function testCases_returnsNonEmptyArray(): void
    {
        $cases = AlarmStateEnum::cases();}
}
