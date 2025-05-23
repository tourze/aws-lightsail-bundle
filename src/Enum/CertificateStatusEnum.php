<?php

namespace AwsLightsailBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 证书状态枚举
 *
 * @see https://docs.aws.amazon.com/lightsail/2016-11-28/api-reference/API_Certificate.html AWS Lightsail API 文档
 */
enum CertificateStatusEnum: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case PENDING_VALIDATION = 'pending_validation';
    case ISSUED = 'issued';
    case INACTIVE = 'inactive';
    case EXPIRED = 'expired';
    case VALIDATION_TIMED_OUT = 'validation_timed_out';
    case REVOKED = 'revoked';
    case FAILED = 'failed';
    case UNKNOWN = 'unknown';

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING_VALIDATION => '等待验证',
            self::ISSUED => '已颁发',
            self::INACTIVE => '未激活',
            self::EXPIRED => '已过期',
            self::VALIDATION_TIMED_OUT => '验证超时',
            self::REVOKED => '已撤销',
            self::FAILED => '失败',
            self::UNKNOWN => '未知',
        };
    }
}
