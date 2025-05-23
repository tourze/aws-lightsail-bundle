<?php

namespace AwsLightsailBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 容器服务计算能力枚举
 *
 * @see https://docs.aws.amazon.com/lightsail/2016-11-28/api-reference/API_ContainerServicePower.html AWS Lightsail 容器服务 API 文档
 */
enum ContainerServicePowerEnum: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case NANO = 'nano';
    case MICRO = 'micro';
    case SMALL = 'small';
    case MEDIUM = 'medium';
    case LARGE = 'large';
    case XLARGE = 'xlarge';

    public function getLabel(): string
    {
        return match ($this) {
            self::NANO => 'Nano (256MB RAM, 0.25 vCPU)',
            self::MICRO => 'Micro (512MB RAM, 0.5 vCPU)',
            self::SMALL => 'Small (1GB RAM, 1 vCPU)',
            self::MEDIUM => 'Medium (2GB RAM, 2 vCPU)',
            self::LARGE => 'Large (4GB RAM, 4 vCPU)',
            self::XLARGE => 'XLarge (8GB RAM, 8 vCPU)',
        };
    }
}
