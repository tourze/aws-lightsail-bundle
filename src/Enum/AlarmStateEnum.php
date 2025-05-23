<?php

namespace AwsLightsailBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 告警状态枚举
 *
 * @see https://docs.aws.amazon.com/lightsail/2016-11-28/api-reference/API_Alarm.html AWS Lightsail 告警 API 文档
 */
enum AlarmStateEnum: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case OK = 'OK';
    case ALARM = 'ALARM';
    case INSUFFICIENT_DATA = 'INSUFFICIENT_DATA';
    case UNKNOWN = 'UNKNOWN';

    public function getLabel(): string
    {
        return match ($this) {
            self::OK => '正常',
            self::ALARM => '告警',
            self::INSUFFICIENT_DATA => '数据不足',
            self::UNKNOWN => '未知',
        };
    }
}
