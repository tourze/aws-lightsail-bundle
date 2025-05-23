<?php

namespace AwsLightsailBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum AmazonRegion: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case US_EAST_2 = 'us-east-2';
    case US_EAST_1 = 'us-east-1';
    case US_WEST_2 = 'us-west-2';
    case AP_SOUTH_1 = 'ap-south-1';
    case AP_SOUTHEAST_1 = 'ap-southeast-1';
    case AP_SOUTHEAST_2 = 'ap-southeast-2';
    case AP_NORTHEAST_1 = 'ap-northeast-1';
    case AP_NORTHEAST_2 = 'ap-northeast-2';
    case CA_CENTRAL_1 = 'ca-central-1';
    case EU_CENTRAL_1 = 'eu-central-1';
    case EU_WEST_1 = 'eu-west-1';
    case EU_WEST_2 = 'eu-west-2';
    case EU_WEST_3 = 'eu-west-3';
    case EU_NORTH_1 = 'eu-north-1';
    case NONE = 'NONE';

    public function getLabel(): string
    {
        return match ($this) {
            self::US_EAST_2 => '美国东部(俄亥俄)',
            self::US_EAST_1 => '美国东部(弗吉尼亚北部)',
            self::US_WEST_2 => '美国西部(俄勒冈)',
            self::AP_SOUTH_1 => '亚太-(孟买)',
            self::AP_SOUTHEAST_1 => '亚太-新加坡(新加坡)',
            self::AP_SOUTHEAST_2 => '亚太-澳大利亚(悉尼)',
            self::AP_NORTHEAST_1 => '亚太-日本(东京)',
            self::AP_NORTHEAST_2 => '亚太-韩国(首尔)',
            self::CA_CENTRAL_1 => '加拿大(加拿大中部)',
            self::EU_CENTRAL_1 => '欧洲-(法兰克福)',
            self::EU_WEST_1 => '欧洲-英国(爱尔兰)',
            self::EU_WEST_2 => '欧洲-英国(伦敦)',
            self::EU_WEST_3 => '欧洲-法国(巴黎)',
            self::EU_NORTH_1 => '欧洲-瑞典(斯德哥尔摩)',
            self::NONE => '无',
        };
    }
}
