<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit\Enum;

use AwsLightsailBundle\Enum\SnapshotTypeEnum;
use PHPUnit\Framework\TestCase;

final class SnapshotTypeEnumTest extends TestCase
{
    public function testEnum_isBackedEnum(): void
    {
        $this->assertTrue(enum_exists(SnapshotTypeEnum::class));
    }

    public function testEnum_hasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\\Enum\\SnapshotTypeEnum', SnapshotTypeEnum::class);
    }public function testCases_returnsNonEmptyArray(): void
    {
        $cases = SnapshotTypeEnum::cases();}
}
