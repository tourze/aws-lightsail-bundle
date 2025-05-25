<?php

declare(strict_types=1);

namespace ChubbyphpTest\AwsLightsailBundle\Unit\Enum;

use AwsLightsailBundle\Enum\InstanceBlueprintEnum;
use PHPUnit\Framework\TestCase;

final class InstanceBlueprintEnumTest extends TestCase
{
    public function testCases(): void
    {
        $expectedCases = [
            'AMAZON_LINUX_2',
            'AMAZON_LINUX_2023',
            'UBUNTU_18_04',
            'UBUNTU_20_04',
            'UBUNTU_22_04',
            'DEBIAN_10',
            'DEBIAN_11',
            'DEBIAN_12',
            'LAMP_UBUNTU_20_04',
            'NGINX_UBUNTU_20_04',
            'WORDPRESS_UBUNTU_20_04',
            'MEAN_UBUNTU_20_04',
            'NODE_JS_UBUNTU_20_04',
            'WINDOWS_SERVER_2019',
            'WINDOWS_SERVER_2022',
        ];

        $actualCases = array_map(fn($case) => $case->name, InstanceBlueprintEnum::cases());

        $this->assertSame($expectedCases, $actualCases);
    }

    public function testValues(): void
    {
        $expectedValues = [
            'amazon_linux_2',
            'amazon_linux_2023',
            'ubuntu_18_04',
            'ubuntu_20_04',
            'ubuntu_22_04',
            'debian_10',
            'debian_11',
            'debian_12',
            'lamp_ubuntu_20_04',
            'nginx_ubuntu_20_04',
            'wordpress_ubuntu_20_04',
            'mean_ubuntu_20_04',
            'nodejs_ubuntu_20_04',
            'windows_server_2019',
            'windows_server_2022',
        ];

        $actualValues = array_map(fn($case) => $case->value, InstanceBlueprintEnum::cases());

        $this->assertSame($expectedValues, $actualValues);
    }

    public function testLabels(): void
    {
        $this->assertSame('Amazon Linux 2', InstanceBlueprintEnum::AMAZON_LINUX_2->getLabel());
        $this->assertSame('Amazon Linux 2023', InstanceBlueprintEnum::AMAZON_LINUX_2023->getLabel());
        $this->assertSame('Ubuntu 18.04 LTS', InstanceBlueprintEnum::UBUNTU_18_04->getLabel());
        $this->assertSame('Ubuntu 20.04 LTS', InstanceBlueprintEnum::UBUNTU_20_04->getLabel());
        $this->assertSame('Ubuntu 22.04 LTS', InstanceBlueprintEnum::UBUNTU_22_04->getLabel());
        $this->assertSame('Debian 10', InstanceBlueprintEnum::DEBIAN_10->getLabel());
        $this->assertSame('Debian 11', InstanceBlueprintEnum::DEBIAN_11->getLabel());
        $this->assertSame('Debian 12', InstanceBlueprintEnum::DEBIAN_12->getLabel());
    }

    public function testApplicationBlueprintLabels(): void
    {
        $this->assertSame('LAMP (Ubuntu 20.04)', InstanceBlueprintEnum::LAMP_UBUNTU_20_04->getLabel());
        $this->assertSame('NGINX (Ubuntu 20.04)', InstanceBlueprintEnum::NGINX_UBUNTU_20_04->getLabel());
        $this->assertSame('WordPress (Ubuntu 20.04)', InstanceBlueprintEnum::WORDPRESS_UBUNTU_20_04->getLabel());
        $this->assertSame('MEAN (Ubuntu 20.04)', InstanceBlueprintEnum::MEAN_UBUNTU_20_04->getLabel());
        $this->assertSame('Node.js (Ubuntu 20.04)', InstanceBlueprintEnum::NODE_JS_UBUNTU_20_04->getLabel());
    }

    public function testWindowsBlueprintLabels(): void
    {
        $this->assertSame('Windows Server 2019', InstanceBlueprintEnum::WINDOWS_SERVER_2019->getLabel());
        $this->assertSame('Windows Server 2022', InstanceBlueprintEnum::WINDOWS_SERVER_2022->getLabel());
    }

    public function testFromStringWithValidValue(): void
    {
        $this->assertSame(InstanceBlueprintEnum::UBUNTU_20_04, InstanceBlueprintEnum::fromString('ubuntu_20_04'));
        $this->assertSame(InstanceBlueprintEnum::WINDOWS_SERVER_2022, InstanceBlueprintEnum::fromString('windows_server_2022'));
        $this->assertSame(InstanceBlueprintEnum::WORDPRESS_UBUNTU_20_04, InstanceBlueprintEnum::fromString('wordpress_ubuntu_20_04'));
        $this->assertSame(InstanceBlueprintEnum::DEBIAN_12, InstanceBlueprintEnum::fromString('debian_12'));
    }

    public function testFromStringWithInvalidValue(): void
    {
        $this->assertSame(InstanceBlueprintEnum::UBUNTU_20_04, InstanceBlueprintEnum::fromString('invalid_blueprint'));
        $this->assertSame(InstanceBlueprintEnum::UBUNTU_20_04, InstanceBlueprintEnum::fromString(''));
        $this->assertSame(InstanceBlueprintEnum::UBUNTU_20_04, InstanceBlueprintEnum::fromString('UBUNTU_20_04'));
    }

    public function testDefaultBlueprint(): void
    {
        $defaultBlueprint = InstanceBlueprintEnum::fromString('invalid');
        $this->assertSame(InstanceBlueprintEnum::UBUNTU_20_04, $defaultBlueprint);
        $this->assertSame('ubuntu_20_04', $defaultBlueprint->value);
        $this->assertSame('Ubuntu 20.04 LTS', $defaultBlueprint->getLabel());
    }

    public function testCasesAreUnique(): void
    {
        $cases = InstanceBlueprintEnum::cases();
        $names = array_map(fn($case) => $case->name, $cases);
        $uniqueNames = array_unique($names);

        $this->assertCount(count($names), $uniqueNames, '所有枚举名称必须唯一');
    }

    public function testValuesAreUnique(): void
    {
        $cases = InstanceBlueprintEnum::cases();
        $values = array_map(fn($case) => $case->value, $cases);
        $uniqueValues = array_unique($values);

        $this->assertCount(count($values), $uniqueValues, '所有枚举值必须唯一');
    }

    public function testLabelsAreUnique(): void
    {
        $cases = InstanceBlueprintEnum::cases();
        $labels = array_map(fn($case) => $case->getLabel(), $cases);
        $uniqueLabels = array_unique($labels);

        $this->assertCount(count($labels), $uniqueLabels, '所有标签必须唯一');
    }

    public function testLinuxBlueprintCount(): void
    {
        $linuxBlueprints = [
            InstanceBlueprintEnum::AMAZON_LINUX_2,
            InstanceBlueprintEnum::AMAZON_LINUX_2023,
            InstanceBlueprintEnum::UBUNTU_18_04,
            InstanceBlueprintEnum::UBUNTU_20_04,
            InstanceBlueprintEnum::UBUNTU_22_04,
            InstanceBlueprintEnum::DEBIAN_10,
            InstanceBlueprintEnum::DEBIAN_11,
            InstanceBlueprintEnum::DEBIAN_12,
        ];

        $this->assertCount(8, $linuxBlueprints, '应该有8个Linux蓝图');
    }

    public function testApplicationBlueprintCount(): void
    {
        $applicationBlueprints = [
            InstanceBlueprintEnum::LAMP_UBUNTU_20_04,
            InstanceBlueprintEnum::NGINX_UBUNTU_20_04,
            InstanceBlueprintEnum::WORDPRESS_UBUNTU_20_04,
            InstanceBlueprintEnum::MEAN_UBUNTU_20_04,
            InstanceBlueprintEnum::NODE_JS_UBUNTU_20_04,
        ];

        $this->assertCount(5, $applicationBlueprints, '应该有5个应用蓝图');
    }

    public function testWindowsBlueprintCount(): void
    {
        $windowsBlueprints = [
            InstanceBlueprintEnum::WINDOWS_SERVER_2019,
            InstanceBlueprintEnum::WINDOWS_SERVER_2022,
        ];

        $this->assertCount(2, $windowsBlueprints, '应该有2个Windows蓝图');
    }

    public function testTotalBlueprintCount(): void
    {
        $this->assertCount(15, InstanceBlueprintEnum::cases(), '总共应该有15个蓝图');
    }
} 