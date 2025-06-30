<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit\Enum;

use AwsLightsailBundle\Enum\DatabaseEngineEnum;
use PHPUnit\Framework\TestCase;

final class DatabaseEngineEnumTest extends TestCase
{
    public function testEnum_isBackedEnum(): void
    {
        $this->assertTrue(enum_exists(DatabaseEngineEnum::class));
    }

    public function testEnum_hasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\\Enum\\DatabaseEngineEnum', DatabaseEngineEnum::class);
    }public function testCases_returnsNonEmptyArray(): void
    {
        $cases = DatabaseEngineEnum::cases();}
}
