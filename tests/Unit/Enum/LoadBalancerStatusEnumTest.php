<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit\Enum;

use AwsLightsailBundle\Enum\LoadBalancerStatusEnum;
use PHPUnit\Framework\TestCase;

final class LoadBalancerStatusEnumTest extends TestCase
{
    public function testEnum_isBackedEnum(): void
    {
        $this->assertTrue(enum_exists(LoadBalancerStatusEnum::class));
    }

    public function testEnum_hasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\\Enum\\LoadBalancerStatusEnum', LoadBalancerStatusEnum::class);
    }public function testCases_returnsNonEmptyArray(): void
    {
        $cases = LoadBalancerStatusEnum::cases();}
}
