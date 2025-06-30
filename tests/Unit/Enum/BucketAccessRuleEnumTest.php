<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit\Enum;

use AwsLightsailBundle\Enum\BucketAccessRuleEnum;
use PHPUnit\Framework\TestCase;

final class BucketAccessRuleEnumTest extends TestCase
{
    public function testEnum_isBackedEnum(): void
    {
        $this->assertTrue(enum_exists(BucketAccessRuleEnum::class));
    }

    public function testEnum_hasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\\Enum\\BucketAccessRuleEnum', BucketAccessRuleEnum::class);
    }public function testCases_returnsNonEmptyArray(): void
    {
        $cases = BucketAccessRuleEnum::cases();}
}
