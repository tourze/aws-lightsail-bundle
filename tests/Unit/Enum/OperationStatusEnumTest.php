<?php

declare(strict_types=1);

namespace ChubbyphpTest\AwsLightsailBundle\Unit\Enum;

use AwsLightsailBundle\Enum\OperationStatusEnum;
use PHPUnit\Framework\TestCase;

final class OperationStatusEnumTest extends TestCase
{
    public function testCases(): void
    {
        $expectedCases = [
            'NOT_STARTED',
            'STARTED',
            'SUCCEEDED',
            'FAILED',
            'COMPLETED',
            'UNKNOWN',
        ];

        $actualCases = array_map(fn($case) => $case->name, OperationStatusEnum::cases());

        $this->assertSame($expectedCases, $actualCases);
    }

    public function testValues(): void
    {
        $expectedValues = [
            'NotStarted',
            'Started',
            'Succeeded',
            'Failed',
            'Completed',
            'Unknown',
        ];

        $actualValues = array_map(fn($case) => $case->value, OperationStatusEnum::cases());

        $this->assertSame($expectedValues, $actualValues);
    }

    public function testLabels(): void
    {
        $this->assertSame('未开始', OperationStatusEnum::NOT_STARTED->getLabel());
        $this->assertSame('已开始', OperationStatusEnum::STARTED->getLabel());
        $this->assertSame('成功', OperationStatusEnum::SUCCEEDED->getLabel());
        $this->assertSame('失败', OperationStatusEnum::FAILED->getLabel());
        $this->assertSame('已完成', OperationStatusEnum::COMPLETED->getLabel());
        $this->assertSame('未知', OperationStatusEnum::UNKNOWN->getLabel());
    }

    public function testEnumValues(): void
    {
        $this->assertSame('NotStarted', OperationStatusEnum::NOT_STARTED->value);
        $this->assertSame('Started', OperationStatusEnum::STARTED->value);
        $this->assertSame('Succeeded', OperationStatusEnum::SUCCEEDED->value);
        $this->assertSame('Failed', OperationStatusEnum::FAILED->value);
        $this->assertSame('Completed', OperationStatusEnum::COMPLETED->value);
        $this->assertSame('Unknown', OperationStatusEnum::UNKNOWN->value);
    }

    public function testCasesAreUnique(): void
    {
        $cases = OperationStatusEnum::cases();
        $names = array_map(fn($case) => $case->name, $cases);
        $uniqueNames = array_unique($names);

        $this->assertCount(count($names), $uniqueNames, '所有枚举名称必须唯一');
    }

    public function testValuesAreUnique(): void
    {
        $cases = OperationStatusEnum::cases();
        $values = array_map(fn($case) => $case->value, $cases);
        $uniqueValues = array_unique($values);

        $this->assertCount(count($values), $uniqueValues, '所有枚举值必须唯一');
    }

    public function testLabelsAreUnique(): void
    {
        $cases = OperationStatusEnum::cases();
        $labels = array_map(fn($case) => $case->getLabel(), $cases);
        $uniqueLabels = array_unique($labels);

        $this->assertCount(count($labels), $uniqueLabels, '所有标签必须唯一');
    }

    public function testTotalStatusCount(): void
    {
        $this->assertCount(6, OperationStatusEnum::cases(), '总共应该有6个操作状态');
    }

    public function testActiveStates(): void
    {
        $activeStates = [
            OperationStatusEnum::NOT_STARTED,
            OperationStatusEnum::STARTED,
        ];

        foreach ($activeStates as $state) {
            $this->assertStringNotContainsString('完成', $state->getLabel());
            $this->assertStringNotContainsString('失败', $state->getLabel());
        }
    }

    public function testFinalStates(): void
    {
        $finalStates = [
            OperationStatusEnum::SUCCEEDED,
            OperationStatusEnum::FAILED,
            OperationStatusEnum::COMPLETED,
        ];

        foreach ($finalStates as $state) {
            $this->assertNotSame('未开始', $state->getLabel());
            $this->assertNotSame('已开始', $state->getLabel());
        }
    }

    public function testSuccessStates(): void
    {
        $successStates = [
            OperationStatusEnum::SUCCEEDED,
            OperationStatusEnum::COMPLETED,
        ];

        foreach ($successStates as $state) {
            $this->assertNotSame('失败', $state->getLabel());
        }
    }

    public function testFailureState(): void
    {
        $this->assertSame('失败', OperationStatusEnum::FAILED->getLabel());
        $this->assertSame('Failed', OperationStatusEnum::FAILED->value);
    }

    public function testPascalCaseValues(): void
    {
        foreach (OperationStatusEnum::cases() as $enum) {
            $value = $enum->value;
            // 检查值是否是 PascalCase 格式
            $this->assertMatchesRegularExpression('/^[A-Z][a-zA-Z]*$/', $value);
        }
    }

    public function testChineseLabelsAreMeaningful(): void
    {
        $this->assertStringContainsString('开始', OperationStatusEnum::NOT_STARTED->getLabel());
        $this->assertStringContainsString('开始', OperationStatusEnum::STARTED->getLabel());
        $this->assertStringContainsString('成功', OperationStatusEnum::SUCCEEDED->getLabel());
        $this->assertStringContainsString('失败', OperationStatusEnum::FAILED->getLabel());
        $this->assertStringContainsString('完成', OperationStatusEnum::COMPLETED->getLabel());
        $this->assertStringContainsString('未知', OperationStatusEnum::UNKNOWN->getLabel());
    }

    public function testAllEnumValues_haveLabels(): void
    {
        foreach (OperationStatusEnum::cases() as $enum) {
            $label = $enum->getLabel();
            $this->assertIsString($label);
            $this->assertNotEmpty($label);
        }
    }
} 