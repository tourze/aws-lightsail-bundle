<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit\Enum;

use AwsLightsailBundle\Enum\ContainerServiceStateEnum;
use PHPUnit\Framework\TestCase;

final class ContainerServiceStateEnumTest extends TestCase
{
    public function testEnum_isBackedEnum(): void
    {
        $this->assertTrue(enum_exists(ContainerServiceStateEnum::class));
    }

    public function testEnum_hasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\\Enum\\ContainerServiceStateEnum', ContainerServiceStateEnum::class);
    }public function testCases_returnsNonEmptyArray(): void
    {
        $cases = ContainerServiceStateEnum::cases();}
}
