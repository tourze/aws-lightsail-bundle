<?php

namespace AwsLightsailBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 负载均衡器状态枚举
 *
 * @see https://docs.aws.amazon.com/lightsail/2016-11-28/api-reference/API_LoadBalancer.html AWS Lightsail 负载均衡器 API 文档
 */
enum LoadBalancerStatusEnum: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case ACTIVE = 'active';
    case PROVISIONING = 'provisioning';
    case FAILED = 'failed';
    case UPDATING = 'updating';
    case DELETING = 'deleting';
    case UNKNOWN = 'unknown';

    public function getLabel(): string
    {
        return match ($this) {
            self::ACTIVE => '活跃',
            self::PROVISIONING => '配置中',
            self::FAILED => '失败',
            self::UPDATING => '更新中',
            self::DELETING => '删除中',
            self::UNKNOWN => '未知',
        };
    }
}
