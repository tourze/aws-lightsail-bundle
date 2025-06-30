<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit\Enum;

use AwsLightsailBundle\Enum\InstanceStateEnum;
use PHPUnit\Framework\TestCase;

final class InstanceStateEnumTest extends TestCase
{
    public function testEnum_isBackedEnum(): void
    {
        $this->assertTrue(enum_exists(InstanceStateEnum::class));
    }

    public function testEnum_hasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\\Enum\\InstanceStateEnum', InstanceStateEnum::class);
    }public function testCases_returnsNonEmptyArray(): void
    {
        $cases = InstanceStateEnum::cases();}
}
