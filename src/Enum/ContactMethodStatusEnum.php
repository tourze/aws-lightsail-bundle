<?php

namespace AwsLightsailBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 联系方式状态枚举
 *
 * @see https://docs.aws.amazon.com/lightsail/2016-11-28/api-reference/API_ContactMethod.html AWS Lightsail 联系方式 API 文档
 */
enum ContactMethodStatusEnum: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case VERIFIED = 'Verified';
    case PENDING = 'Pending';
    case FAILED = 'Failed';

    public function getLabel(): string
    {
        return match ($this) {
            self::VERIFIED => '已验证',
            self::PENDING => '等待验证',
            self::FAILED => '验证失败',
        };
    }
}
