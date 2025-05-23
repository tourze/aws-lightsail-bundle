<?php

namespace AwsLightsailBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 磁盘状态枚举
 *
 * @see https://docs.aws.amazon.com/lightsail/2016-11-28/api-reference/API_Disk.html AWS Lightsail 磁盘 API 文档
 */
enum DiskStateEnum: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case CREATING = 'creating';
    case AVAILABLE = 'available';
    case IN_USE = 'in-use';
    case DELETING = 'deleting';
    case DELETED = 'deleted';
    case ERROR = 'error';
    case UNKNOWN = 'unknown';

    public function getLabel(): string
    {
        return match ($this) {
            self::CREATING => '创建中',
            self::AVAILABLE => '可用',
            self::IN_USE => '使用中',
            self::DELETING => '删除中',
            self::DELETED => '已删除',
            self::ERROR => '错误',
            self::UNKNOWN => '未知',
        };
    }
}
