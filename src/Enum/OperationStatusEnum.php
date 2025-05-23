<?php

namespace AwsLightsailBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 操作状态枚举
 *
 * @see https://docs.aws.amazon.com/lightsail/2016-11-28/api-reference/API_Operation.html AWS Lightsail 操作 API 文档
 */
enum OperationStatusEnum: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case NOT_STARTED = 'NotStarted';
    case STARTED = 'Started';
    case SUCCEEDED = 'Succeeded';
    case FAILED = 'Failed';
    case COMPLETED = 'Completed';
    case UNKNOWN = 'Unknown';

    public function getLabel(): string
    {
        return match ($this) {
            self::NOT_STARTED => '未开始',
            self::STARTED => '已开始',
            self::SUCCEEDED => '成功',
            self::FAILED => '失败',
            self::COMPLETED => '已完成',
            self::UNKNOWN => '未知',
        };
    }
}
