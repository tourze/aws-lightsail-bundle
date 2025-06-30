<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit\Enum;

use AwsLightsailBundle\Enum\DatabaseStatusEnum;
use PHPUnit\Framework\TestCase;

final class DatabaseStatusEnumTest extends TestCase
{
    public function testEnum_isBackedEnum(): void
    {
        $this->assertTrue(enum_exists(DatabaseStatusEnum::class));
    }

    public function testEnum_hasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\\Enum\\DatabaseStatusEnum', DatabaseStatusEnum::class);
    }public function testCases_returnsNonEmptyArray(): void
    {
        $cases = DatabaseStatusEnum::cases();}
}
