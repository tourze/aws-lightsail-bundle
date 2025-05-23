<?php

namespace AwsLightsailBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * DNS 记录类型枚举
 *
 * @see https://docs.aws.amazon.com/lightsail/2016-11-28/api-reference/API_DomainEntry.html AWS Lightsail 域名条目 API 文档
 */
enum DnsRecordTypeEnum: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case A = 'A';
    case AAAA = 'AAAA';
    case CNAME = 'CNAME';
    case MX = 'MX';
    case NS = 'NS';
    case SOA = 'SOA';
    case SRV = 'SRV';
    case TXT = 'TXT';
    case CAA = 'CAA';

    public function getLabel(): string
    {
        return match ($this) {
            self::A => 'A 记录 (IPv4)',
            self::AAAA => 'AAAA 记录 (IPv6)',
            self::CNAME => 'CNAME 记录',
            self::MX => 'MX 记录 (邮件)',
            self::NS => 'NS 记录 (域名服务器)',
            self::SOA => 'SOA 记录 (授权起始)',
            self::SRV => 'SRV 记录 (服务)',
            self::TXT => 'TXT 记录 (文本)',
            self::CAA => 'CAA 记录 (证书授权)',
        };
    }
}
