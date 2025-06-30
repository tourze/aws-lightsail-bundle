<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit\Enum;

use AwsLightsailBundle\Enum\AmazonRegion;
use PHPUnit\Framework\TestCase;

final class AmazonRegionTest extends TestCase
{
    public function testEnum_isBackedEnum(): void
    {
        $this->assertTrue(enum_exists(AmazonRegion::class));
    }

    public function testEnum_hasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\\Enum\\AmazonRegion', AmazonRegion::class);
    }public function testCases_returnsNonEmptyArray(): void
    {
        $cases = AmazonRegion::cases();}
}
