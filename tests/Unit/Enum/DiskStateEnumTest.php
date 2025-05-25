<?php

declare(strict_types=1);

namespace ChubbyphpTest\AwsLightsailBundle\Unit\Enum;

use AwsLightsailBundle\Enum\DiskStateEnum;
use PHPUnit\Framework\TestCase;

final class DiskStateEnumTest extends TestCase
{
    public function testCases(): void
    {
        $expectedCases = [
            'CREATING',
            'AVAILABLE',
            'IN_USE',
            'DELETING',
            'DELETED',
            'ERROR',
            'UNKNOWN',
        ];

        $actualCases = array_map(fn($case) => $case->name, DiskStateEnum::cases());

        $this->assertSame($expectedCases, $actualCases);
    }

    public function testValues(): void
    {
        $expectedValues = [
            'creating',
            'available',
            'in-use',
            'deleting',
            'deleted',
            'error',
            'unknown',
        ];

        $actualValues = array_map(fn($case) => $case->value, DiskStateEnum::cases());

        $this->assertSame($expectedValues, $actualValues);
    }

    public function testLabels(): void
    {
        $this->assertSame('创建中', DiskStateEnum::CREATING->getLabel());
        $this->assertSame('可用', DiskStateEnum::AVAILABLE->getLabel());
        $this->assertSame('使用中', DiskStateEnum::IN_USE->getLabel());
        $this->assertSame('删除中', DiskStateEnum::DELETING->getLabel());
        $this->assertSame('已删除', DiskStateEnum::DELETED->getLabel());
        $this->assertSame('错误', DiskStateEnum::ERROR->getLabel());
        $this->assertSame('未知', DiskStateEnum::UNKNOWN->getLabel());
    }

    public function testEnumValues(): void
    {
        $this->assertSame('creating', DiskStateEnum::CREATING->value);
        $this->assertSame('available', DiskStateEnum::AVAILABLE->value);
        $this->assertSame('in-use', DiskStateEnum::IN_USE->value);
        $this->assertSame('deleting', DiskStateEnum::DELETING->value);
        $this->assertSame('deleted', DiskStateEnum::DELETED->value);
        $this->assertSame('error', DiskStateEnum::ERROR->value);
        $this->assertSame('unknown', DiskStateEnum::UNKNOWN->value);
    }

    public function testCasesAreUnique(): void
    {
        $cases = DiskStateEnum::cases();
        $names = array_map(fn($case) => $case->name, $cases);
        $uniqueNames = array_unique($names);

        $this->assertCount(count($names), $uniqueNames, '所有枚举名称必须唯一');
    }

    public function testValuesAreUnique(): void
    {
        $cases = DiskStateEnum::cases();
        $values = array_map(fn($case) => $case->value, $cases);
        $uniqueValues = array_unique($values);

        $this->assertCount(count($values), $uniqueValues, '所有枚举值必须唯一');
    }

    public function testLabelsAreUnique(): void
    {
        $cases = DiskStateEnum::cases();
        $labels = array_map(fn($case) => $case->getLabel(), $cases);
        $uniqueLabels = array_unique($labels);

        $this->assertCount(count($labels), $uniqueLabels, '所有标签必须唯一');
    }

    public function testAllEnumValues_haveLabels(): void
    {
        foreach (DiskStateEnum::cases() as $enum) {
            $label = $enum->getLabel();
            $this->assertIsString($label);
            $this->assertNotEmpty($label);
        }
    }

    public function testTotalStateCount(): void
    {
        $this->assertCount(7, DiskStateEnum::cases(), '总共应该有7个磁盘状态');
    }

    public function testOperationalStates(): void
    {
        $operationalStates = [
            DiskStateEnum::CREATING,
            DiskStateEnum::AVAILABLE,
            DiskStateEnum::IN_USE,
        ];

        foreach ($operationalStates as $state) {
            $this->assertNotSame('错误', $state->getLabel());
            $this->assertNotSame('未知', $state->getLabel());
        }
    }

    public function testTerminalStates(): void
    {
        $terminalStates = [
            DiskStateEnum::DELETED,
            DiskStateEnum::ERROR,
        ];
        
        $terminalLabels = ['已删除', '错误'];

        foreach ($terminalStates as $state) {
            $this->assertTrue(in_array($state->getLabel(), $terminalLabels, true));
        }
    }

    public function testTransitionalStates(): void
    {
        $transitionalStates = [
            DiskStateEnum::CREATING,
            DiskStateEnum::DELETING,
        ];

        foreach ($transitionalStates as $state) {
            $this->assertStringContainsString('中', $state->getLabel());
        }
    }

    public function testEnumImplementsExpectedInterfaces(): void
    {
        $enum = DiskStateEnum::AVAILABLE;
        
        $this->assertInstanceOf(\BackedEnum::class, $enum);
        $this->assertTrue(method_exists($enum, 'getLabel'));
    }

    public function testStringRepresentation(): void
    {
        foreach (DiskStateEnum::cases() as $enum) {
            $this->assertSame($enum->value, (string) $enum->value);
        }
    }

    public function testInUseStateHasHyphen(): void
    {
        $this->assertSame('in-use', DiskStateEnum::IN_USE->value);
        $this->assertStringContainsString('-', DiskStateEnum::IN_USE->value);
    }

    public function testChineseLabelsAreMeaningful(): void
    {
        $expectedMeanings = [
            'CREATING' => '创建',
            'AVAILABLE' => '可用',
            'IN_USE' => '使用',
            'DELETING' => '删除',
            'DELETED' => '已删除',
            'ERROR' => '错误',
            'UNKNOWN' => '未知',
        ];

        foreach (DiskStateEnum::cases() as $state) {
            $caseName = $state->name;
            $expectedMeaning = $expectedMeanings[$caseName];
            $this->assertStringContainsString($expectedMeaning, $state->getLabel());
        }
    }
} 