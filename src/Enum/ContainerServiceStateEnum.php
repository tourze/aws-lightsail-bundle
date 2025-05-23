<?php

namespace AwsLightsailBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 容器服务状态枚举
 *
 * @see https://docs.aws.amazon.com/lightsail/2016-11-28/api-reference/API_ContainerService.html AWS Lightsail 容器服务 API 文档
 */
enum ContainerServiceStateEnum: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case PENDING = 'PENDING';
    case READY = 'READY';
    case RUNNING = 'RUNNING';
    case UPDATING = 'UPDATING';
    case DELETING = 'DELETING';
    case DISABLED = 'DISABLED';
    case DEPLOYING = 'DEPLOYING';
    case FAILED = 'FAILED';
    case UNKNOWN = 'UNKNOWN';

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => '等待中',
            self::READY => '就绪',
            self::RUNNING => '运行中',
            self::UPDATING => '更新中',
            self::DELETING => '删除中',
            self::DISABLED => '已禁用',
            self::DEPLOYING => '部署中',
            self::FAILED => '失败',
            self::UNKNOWN => '未知',
        };
    }
}
