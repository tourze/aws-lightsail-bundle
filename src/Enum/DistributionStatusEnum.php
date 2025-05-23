<?php

namespace AwsLightsailBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 分发状态枚举
 *
 * @see https://docs.aws.amazon.com/lightsail/2016-11-28/api-reference/API_LightsailDistribution.html AWS Lightsail 分发 API 文档
 */
enum DistributionStatusEnum: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case CREATING = 'creating';
    case ACTIVE = 'active';
    case UPDATING = 'updating';
    case DELETING = 'deleting';
    case FAILED = 'failed';
    case PENDING = 'pending';

    public function getLabel(): string
    {
        return match ($this) {
            self::CREATING => '创建中',
            self::ACTIVE => '活跃',
            self::UPDATING => '更新中',
            self::DELETING => '删除中',
            self::FAILED => '失败',
            self::PENDING => '等待中',
        };
    }
}
