<?php

namespace AwsLightsailBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 联系方式类型枚举
 *
 * @see https://docs.aws.amazon.com/lightsail/2016-11-28/api-reference/API_ContactMethod.html AWS Lightsail 联系方式 API 文档
 */
enum ContactMethodTypeEnum: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case EMAIL = 'Email';
    case SMS = 'SMS';

    public function getLabel(): string
    {
        return match ($this) {
            self::EMAIL => '电子邮箱',
            self::SMS => '短信',
        };
    }
}
