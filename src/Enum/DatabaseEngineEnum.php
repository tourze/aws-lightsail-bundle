<?php

namespace AwsLightsailBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 数据库引擎枚举
 *
 * @see https://docs.aws.amazon.com/lightsail/2016-11-28/api-reference/API_RelationalDatabaseEngine.html AWS Lightsail 数据库 API 文档
 */
enum DatabaseEngineEnum: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case MYSQL = 'mysql';
    case POSTGRES = 'postgres';

    public function getLabel(): string
    {
        return match ($this) {
            self::MYSQL => 'MySQL',
            self::POSTGRES => 'PostgreSQL',
        };
    }
}
