<?php

namespace AwsLightsailBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 操作类型枚举
 *
 * @see https://docs.aws.amazon.com/lightsail/2016-11-28/api-reference/API_Operation.html AWS Lightsail 操作 API 文档
 */
enum OperationTypeEnum: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case CREATE_INSTANCE = 'CreateInstance';
    case DELETE_INSTANCE = 'DeleteInstance';
    case START_INSTANCE = 'StartInstance';
    case STOP_INSTANCE = 'StopInstance';
    case REBOOT_INSTANCE = 'RebootInstance';
    case CREATE_DISK = 'CreateDisk';
    case DELETE_DISK = 'DeleteDisk';
    case ATTACH_DISK = 'AttachDisk';
    case DETACH_DISK = 'DetachDisk';
    case CREATE_SNAPSHOT = 'CreateSnapshot';
    case DELETE_SNAPSHOT = 'DeleteSnapshot';
    case CREATE_DISTRIBUTION = 'CreateDistribution';
    case DELETE_DISTRIBUTION = 'DeleteDistribution';
    case CREATE_DOMAIN = 'CreateDomain';
    case DELETE_DOMAIN = 'DeleteDomain';
    case CREATE_DATABASE = 'CreateDatabase';
    case DELETE_DATABASE = 'DeleteDatabase';
    case CREATE_LOAD_BALANCER = 'CreateLoadBalancer';
    case DELETE_LOAD_BALANCER = 'DeleteLoadBalancer';
    case OTHER = 'Other';

    public function getLabel(): string
    {
        return match ($this) {
            self::CREATE_INSTANCE => '创建实例',
            self::DELETE_INSTANCE => '删除实例',
            self::START_INSTANCE => '启动实例',
            self::STOP_INSTANCE => '停止实例',
            self::REBOOT_INSTANCE => '重启实例',
            self::CREATE_DISK => '创建磁盘',
            self::DELETE_DISK => '删除磁盘',
            self::ATTACH_DISK => '挂载磁盘',
            self::DETACH_DISK => '分离磁盘',
            self::CREATE_SNAPSHOT => '创建快照',
            self::DELETE_SNAPSHOT => '删除快照',
            self::CREATE_DISTRIBUTION => '创建分发',
            self::DELETE_DISTRIBUTION => '删除分发',
            self::CREATE_DOMAIN => '创建域名',
            self::DELETE_DOMAIN => '删除域名',
            self::CREATE_DATABASE => '创建数据库',
            self::DELETE_DATABASE => '删除数据库',
            self::CREATE_LOAD_BALANCER => '创建负载均衡器',
            self::DELETE_LOAD_BALANCER => '删除负载均衡器',
            self::OTHER => '其他操作',
        };
    }
}
