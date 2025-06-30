<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit\Enum;

use AwsLightsailBundle\Enum\ContainerServicePowerEnum;
use PHPUnit\Framework\TestCase;

final class ContainerServicePowerEnumTest extends TestCase
{
    public function testEnum_isBackedEnum(): void
    {
        $this->assertTrue(enum_exists(ContainerServicePowerEnum::class));
    }

    public function testEnum_hasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\\Enum\\ContainerServicePowerEnum', ContainerServicePowerEnum::class);
    }public function testCases_returnsNonEmptyArray(): void
    {
        $cases = ContainerServicePowerEnum::cases();}
}
