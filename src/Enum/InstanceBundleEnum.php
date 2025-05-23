<?php

namespace AwsLightsailBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 实例套餐枚举
 *
 * @see https://docs.aws.amazon.com/lightsail/2016-11-28/api-reference/API_Bundle.html AWS Lightsail 套餐 API 文档
 */
enum InstanceBundleEnum: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    // 通用实例套餐
    case NANO_2_0 = 'nano_2_0';
    case MICRO_2_0 = 'micro_2_0';
    case SMALL_2_0 = 'small_2_0';
    case MEDIUM_2_0 = 'medium_2_0';
    case LARGE_2_0 = 'large_2_0';
    case XLARGE_2_0 = 'xlarge_2_0';
    case XXLARGE_2_0 = '2xlarge_2_0';

    // 优化型实例套餐
    case NANO_3_0 = 'nano_3_0';
    case MICRO_3_0 = 'micro_3_0';
    case SMALL_3_0 = 'small_3_0';
    case MEDIUM_3_0 = 'medium_3_0';
    case LARGE_3_0 = 'large_3_0';
    case XLARGE_3_0 = 'xlarge_3_0';
    case XXLARGE_3_0 = '2xlarge_3_0';

    public function getLabel(): string
    {
        return match ($this) {
            self::NANO_2_0 => 'Nano (第二代: 1 vCPU, 512MB, 20GB)',
            self::MICRO_2_0 => 'Micro (第二代: 1 vCPU, 1GB, 40GB)',
            self::SMALL_2_0 => 'Small (第二代: 1 vCPU, 2GB, 60GB)',
            self::MEDIUM_2_0 => 'Medium (第二代: 2 vCPU, 4GB, 80GB)',
            self::LARGE_2_0 => 'Large (第二代: 2 vCPU, 8GB, 160GB)',
            self::XLARGE_2_0 => 'XLarge (第二代: 4 vCPU, 16GB, 320GB)',
            self::XXLARGE_2_0 => '2XLarge (第二代: 8 vCPU, 32GB, 640GB)',
            self::NANO_3_0 => 'Nano (第三代: 2 vCPU, 512MB, 20GB)',
            self::MICRO_3_0 => 'Micro (第三代: 2 vCPU, 1GB, 40GB)',
            self::SMALL_3_0 => 'Small (第三代: 2 vCPU, 2GB, 60GB)',
            self::MEDIUM_3_0 => 'Medium (第三代: 2 vCPU, 4GB, 80GB)',
            self::LARGE_3_0 => 'Large (第三代: 2 vCPU, 8GB, 160GB)',
            self::XLARGE_3_0 => 'XLarge (第三代: 4 vCPU, 16GB, 320GB)',
            self::XXLARGE_3_0 => '2XLarge (第三代: 8 vCPU, 32GB, 640GB)',
        };
    }

    /**
     * 从字符串ID转换为对应的枚举值
     */
    public static function fromString(string $bundleId): self
    {
        return match ($bundleId) {
            'nano_2_0' => self::NANO_2_0,
            'micro_2_0' => self::MICRO_2_0,
            'small_2_0' => self::SMALL_2_0,
            'medium_2_0' => self::MEDIUM_2_0,
            'large_2_0' => self::LARGE_2_0,
            'xlarge_2_0' => self::XLARGE_2_0,
            '2xlarge_2_0' => self::XXLARGE_2_0,
            'nano_3_0' => self::NANO_3_0,
            'micro_3_0' => self::MICRO_3_0,
            'small_3_0' => self::SMALL_3_0,
            'medium_3_0' => self::MEDIUM_3_0,
            'large_3_0' => self::LARGE_3_0,
            'xlarge_3_0' => self::XLARGE_3_0,
            '2xlarge_3_0' => self::XXLARGE_3_0,
            default => self::MICRO_2_0, // 默认值
        };
    }
}
