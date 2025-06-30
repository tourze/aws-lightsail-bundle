<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit\Enum;

use AwsLightsailBundle\Enum\OperationTypeEnum;
use PHPUnit\Framework\TestCase;

final class OperationTypeEnumTest extends TestCase
{
    public function testEnum_isBackedEnum(): void
    {
        $this->assertTrue(enum_exists(OperationTypeEnum::class));
    }

    public function testEnum_hasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\\Enum\\OperationTypeEnum', OperationTypeEnum::class);
    }public function testCases_returnsNonEmptyArray(): void
    {
        $cases = OperationTypeEnum::cases();}
}
