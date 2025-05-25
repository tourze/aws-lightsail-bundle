<?php

declare(strict_types=1);

namespace ChubbyphpTest\AwsLightsailBundle\Unit\Enum;

use AwsLightsailBundle\Enum\InstanceBundleEnum;
use PHPUnit\Framework\TestCase;

final class InstanceBundleEnumTest extends TestCase
{
    public function testCases(): void
    {
        $expectedCases = [
            'NANO_2_0',
            'MICRO_2_0',
            'SMALL_2_0',
            'MEDIUM_2_0',
            'LARGE_2_0',
            'XLARGE_2_0',
            'XXLARGE_2_0',
            'NANO_3_0',
            'MICRO_3_0',
            'SMALL_3_0',
            'MEDIUM_3_0',
            'LARGE_3_0',
            'XLARGE_3_0',
            'XXLARGE_3_0',
        ];

        $actualCases = array_map(fn($case) => $case->name, InstanceBundleEnum::cases());

        $this->assertSame($expectedCases, $actualCases);
    }

    public function testValues(): void
    {
        $expectedValues = [
            'nano_2_0',
            'micro_2_0',
            'small_2_0',
            'medium_2_0',
            'large_2_0',
            'xlarge_2_0',
            '2xlarge_2_0',
            'nano_3_0',
            'micro_3_0',
            'small_3_0',
            'medium_3_0',
            'large_3_0',
            'xlarge_3_0',
            '2xlarge_3_0',
        ];

        $actualValues = array_map(fn($case) => $case->value, InstanceBundleEnum::cases());

        $this->assertSame($expectedValues, $actualValues);
    }

    public function testSecondGenerationLabels(): void
    {
        $this->assertSame('Nano (第二代: 1 vCPU, 512MB, 20GB)', InstanceBundleEnum::NANO_2_0->getLabel());
        $this->assertSame('Micro (第二代: 1 vCPU, 1GB, 40GB)', InstanceBundleEnum::MICRO_2_0->getLabel());
        $this->assertSame('Small (第二代: 1 vCPU, 2GB, 60GB)', InstanceBundleEnum::SMALL_2_0->getLabel());
        $this->assertSame('Medium (第二代: 2 vCPU, 4GB, 80GB)', InstanceBundleEnum::MEDIUM_2_0->getLabel());
        $this->assertSame('Large (第二代: 2 vCPU, 8GB, 160GB)', InstanceBundleEnum::LARGE_2_0->getLabel());
        $this->assertSame('XLarge (第二代: 4 vCPU, 16GB, 320GB)', InstanceBundleEnum::XLARGE_2_0->getLabel());
        $this->assertSame('2XLarge (第二代: 8 vCPU, 32GB, 640GB)', InstanceBundleEnum::XXLARGE_2_0->getLabel());
    }

    public function testThirdGenerationLabels(): void
    {
        $this->assertSame('Nano (第三代: 2 vCPU, 512MB, 20GB)', InstanceBundleEnum::NANO_3_0->getLabel());
        $this->assertSame('Micro (第三代: 2 vCPU, 1GB, 40GB)', InstanceBundleEnum::MICRO_3_0->getLabel());
        $this->assertSame('Small (第三代: 2 vCPU, 2GB, 60GB)', InstanceBundleEnum::SMALL_3_0->getLabel());
        $this->assertSame('Medium (第三代: 2 vCPU, 4GB, 80GB)', InstanceBundleEnum::MEDIUM_3_0->getLabel());
        $this->assertSame('Large (第三代: 2 vCPU, 8GB, 160GB)', InstanceBundleEnum::LARGE_3_0->getLabel());
        $this->assertSame('XLarge (第三代: 4 vCPU, 16GB, 320GB)', InstanceBundleEnum::XLARGE_3_0->getLabel());
        $this->assertSame('2XLarge (第三代: 8 vCPU, 32GB, 640GB)', InstanceBundleEnum::XXLARGE_3_0->getLabel());
    }

    public function testFromStringWithValidSecondGenValues(): void
    {
        $this->assertSame(InstanceBundleEnum::NANO_2_0, InstanceBundleEnum::fromString('nano_2_0'));
        $this->assertSame(InstanceBundleEnum::MICRO_2_0, InstanceBundleEnum::fromString('micro_2_0'));
        $this->assertSame(InstanceBundleEnum::SMALL_2_0, InstanceBundleEnum::fromString('small_2_0'));
        $this->assertSame(InstanceBundleEnum::MEDIUM_2_0, InstanceBundleEnum::fromString('medium_2_0'));
        $this->assertSame(InstanceBundleEnum::LARGE_2_0, InstanceBundleEnum::fromString('large_2_0'));
        $this->assertSame(InstanceBundleEnum::XLARGE_2_0, InstanceBundleEnum::fromString('xlarge_2_0'));
        $this->assertSame(InstanceBundleEnum::XXLARGE_2_0, InstanceBundleEnum::fromString('2xlarge_2_0'));
    }

    public function testFromStringWithValidThirdGenValues(): void
    {
        $this->assertSame(InstanceBundleEnum::NANO_3_0, InstanceBundleEnum::fromString('nano_3_0'));
        $this->assertSame(InstanceBundleEnum::MICRO_3_0, InstanceBundleEnum::fromString('micro_3_0'));
        $this->assertSame(InstanceBundleEnum::SMALL_3_0, InstanceBundleEnum::fromString('small_3_0'));
        $this->assertSame(InstanceBundleEnum::MEDIUM_3_0, InstanceBundleEnum::fromString('medium_3_0'));
        $this->assertSame(InstanceBundleEnum::LARGE_3_0, InstanceBundleEnum::fromString('large_3_0'));
        $this->assertSame(InstanceBundleEnum::XLARGE_3_0, InstanceBundleEnum::fromString('xlarge_3_0'));
        $this->assertSame(InstanceBundleEnum::XXLARGE_3_0, InstanceBundleEnum::fromString('2xlarge_3_0'));
    }

    public function testFromStringWithInvalidValue(): void
    {
        $this->assertSame(InstanceBundleEnum::MICRO_2_0, InstanceBundleEnum::fromString('invalid_bundle'));
        $this->assertSame(InstanceBundleEnum::MICRO_2_0, InstanceBundleEnum::fromString(''));
        $this->assertSame(InstanceBundleEnum::MICRO_2_0, InstanceBundleEnum::fromString('MICRO_2_0'));
        $this->assertSame(InstanceBundleEnum::MICRO_2_0, InstanceBundleEnum::fromString('micro-2-0'));
    }

    public function testDefaultBundle(): void
    {
        $defaultBundle = InstanceBundleEnum::fromString('non_existent_bundle');
        $this->assertSame(InstanceBundleEnum::MICRO_2_0, $defaultBundle);
        $this->assertSame('micro_2_0', $defaultBundle->value);
        $this->assertSame('Micro (第二代: 1 vCPU, 1GB, 40GB)', $defaultBundle->getLabel());
    }

    public function testCasesAreUnique(): void
    {
        $cases = InstanceBundleEnum::cases();
        $names = array_map(fn($case) => $case->name, $cases);
        $uniqueNames = array_unique($names);

        $this->assertCount(count($names), $uniqueNames, '所有枚举名称必须唯一');
    }

    public function testValuesAreUnique(): void
    {
        $cases = InstanceBundleEnum::cases();
        $values = array_map(fn($case) => $case->value, $cases);
        $uniqueValues = array_unique($values);

        $this->assertCount(count($values), $uniqueValues, '所有枚举值必须唯一');
    }

    public function testLabelsAreUnique(): void
    {
        $cases = InstanceBundleEnum::cases();
        $labels = array_map(fn($case) => $case->getLabel(), $cases);
        $uniqueLabels = array_unique($labels);

        $this->assertCount(count($labels), $uniqueLabels, '所有标签必须唯一');
    }

    public function testSecondGenerationBundleCount(): void
    {
        $secondGenBundles = [
            InstanceBundleEnum::NANO_2_0,
            InstanceBundleEnum::MICRO_2_0,
            InstanceBundleEnum::SMALL_2_0,
            InstanceBundleEnum::MEDIUM_2_0,
            InstanceBundleEnum::LARGE_2_0,
            InstanceBundleEnum::XLARGE_2_0,
            InstanceBundleEnum::XXLARGE_2_0,
        ];

        $this->assertCount(7, $secondGenBundles, '应该有7个第二代套餐');
    }

    public function testThirdGenerationBundleCount(): void
    {
        $thirdGenBundles = [
            InstanceBundleEnum::NANO_3_0,
            InstanceBundleEnum::MICRO_3_0,
            InstanceBundleEnum::SMALL_3_0,
            InstanceBundleEnum::MEDIUM_3_0,
            InstanceBundleEnum::LARGE_3_0,
            InstanceBundleEnum::XLARGE_3_0,
            InstanceBundleEnum::XXLARGE_3_0,
        ];

        $this->assertCount(7, $thirdGenBundles, '应该有7个第三代套餐');
    }

    public function testTotalBundleCount(): void
    {
        $this->assertCount(14, InstanceBundleEnum::cases(), '总共应该有14个套餐');
    }

    public function testFromStringWithAllValidEnumValues(): void
    {
        foreach (InstanceBundleEnum::cases() as $expectedEnum) {
            $actualEnum = InstanceBundleEnum::fromString($expectedEnum->value);
            $this->assertSame($expectedEnum, $actualEnum);
        }
    }

    public function testSecondGenerationLabelsContainCorrectText(): void
    {
        $secondGenBundles = [
            InstanceBundleEnum::NANO_2_0,
            InstanceBundleEnum::MICRO_2_0,
            InstanceBundleEnum::SMALL_2_0,
            InstanceBundleEnum::MEDIUM_2_0,
            InstanceBundleEnum::LARGE_2_0,
            InstanceBundleEnum::XLARGE_2_0,
            InstanceBundleEnum::XXLARGE_2_0,
        ];

        foreach ($secondGenBundles as $bundle) {
            $this->assertStringContainsString('第二代', $bundle->getLabel());
            $this->assertStringContainsString('vCPU', $bundle->getLabel());
            $this->assertStringContainsString('GB', $bundle->getLabel());
        }
    }

    public function testThirdGenerationLabelsContainCorrectText(): void
    {
        $thirdGenBundles = [
            InstanceBundleEnum::NANO_3_0,
            InstanceBundleEnum::MICRO_3_0,
            InstanceBundleEnum::SMALL_3_0,
            InstanceBundleEnum::MEDIUM_3_0,
            InstanceBundleEnum::LARGE_3_0,
            InstanceBundleEnum::XLARGE_3_0,
            InstanceBundleEnum::XXLARGE_3_0,
        ];

        foreach ($thirdGenBundles as $bundle) {
            $this->assertStringContainsString('第三代', $bundle->getLabel());
            $this->assertStringContainsString('vCPU', $bundle->getLabel());
            $this->assertStringContainsString('GB', $bundle->getLabel());
        }
    }

    public function testFromStringWithSpecialCharacters(): void
    {
        $this->assertSame(InstanceBundleEnum::MICRO_2_0, InstanceBundleEnum::fromString(' micro_2_0 '));
        $this->assertSame(InstanceBundleEnum::MICRO_2_0, InstanceBundleEnum::fromString('micro_2_0\n'));
        $this->assertSame(InstanceBundleEnum::MICRO_2_0, InstanceBundleEnum::fromString('micro.2.0'));
        $this->assertSame(InstanceBundleEnum::MICRO_2_0, InstanceBundleEnum::fromString('micro-2-0'));
    }

    public function testFromStringWithEmptyAndNullLikeValues(): void
    {
        $this->assertSame(InstanceBundleEnum::MICRO_2_0, InstanceBundleEnum::fromString(''));
        $this->assertSame(InstanceBundleEnum::MICRO_2_0, InstanceBundleEnum::fromString('0'));
        $this->assertSame(InstanceBundleEnum::MICRO_2_0, InstanceBundleEnum::fromString('false'));
        $this->assertSame(InstanceBundleEnum::MICRO_2_0, InstanceBundleEnum::fromString('null'));
    }
} 