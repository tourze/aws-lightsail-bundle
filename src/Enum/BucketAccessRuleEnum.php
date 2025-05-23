<?php

namespace AwsLightsailBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 存储桶访问规则枚举
 *
 * @see https://docs.aws.amazon.com/lightsail/2016-11-28/api-reference/API_GetBucketAccessKeys.html AWS Lightsail 存储桶 API 文档
 */
enum BucketAccessRuleEnum: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case PUBLIC_READ = 'public_read';
    case PRIVATE = 'private';

    public function getLabel(): string
    {
        return match ($this) {
            self::PUBLIC_READ => '公开读取',
            self::PRIVATE => '私有',
        };
    }
}
