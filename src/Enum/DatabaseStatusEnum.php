<?php

namespace AwsLightsailBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 数据库状态枚举
 *
 * @see https://docs.aws.amazon.com/lightsail/2016-11-28/api-reference/API_RelationalDatabase.html AWS Lightsail 数据库 API 文档
 */
enum DatabaseStatusEnum: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case CREATING = 'creating';
    case AVAILABLE = 'available';
    case MODIFYING = 'modifying';
    case UPDATING = 'updating';
    case DELETING = 'deleting';
    case STOPPING = 'stopping';
    case STOPPED = 'stopped';
    case STARTING = 'starting';
    case FAILING_OVER = 'failing-over';
    case BACKUP_RESTORE = 'backup-restore';
    case CONFIGURING_LOG_EXPORT = 'configuring-log-export';
    case MAINTENANCE = 'maintenance';
    case REBOOTING = 'rebooting';
    case RESETTING_MASTER_CREDENTIALS = 'resetting-master-credentials';
    case UPGRADING = 'upgrading';
    case UNKNOWN = 'unknown';

    public function getLabel(): string
    {
        return match ($this) {
            self::CREATING => '创建中',
            self::AVAILABLE => '可用的',
            self::MODIFYING => '修改中',
            self::UPDATING => '更新中',
            self::DELETING => '删除中',
            self::STOPPING => '停止中',
            self::STOPPED => '已停止',
            self::STARTING => '启动中',
            self::FAILING_OVER => '故障转移中',
            self::BACKUP_RESTORE => '备份恢复中',
            self::CONFIGURING_LOG_EXPORT => '配置日志导出中',
            self::MAINTENANCE => '维护中',
            self::REBOOTING => '重启中',
            self::RESETTING_MASTER_CREDENTIALS => '重置主凭证中',
            self::UPGRADING => '升级中',
            self::UNKNOWN => '未知',
        };
    }
}
