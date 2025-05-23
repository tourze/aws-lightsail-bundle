<?php

namespace AwsLightsailBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 快照类型枚举
 *
 * @see https://docs.aws.amazon.com/lightsail/2016-11-28/api-reference/API_GetInstanceSnapshot.html AWS Lightsail 快照 API 文档
 */
enum SnapshotTypeEnum: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case INSTANCE = 'instance';
    case DISK = 'disk';
    case DATABASE = 'database';

    public function getLabel(): string
    {
        return match ($this) {
            self::INSTANCE => '实例快照',
            self::DISK => '磁盘快照',
            self::DATABASE => '数据库快照',
        };
    }
}
