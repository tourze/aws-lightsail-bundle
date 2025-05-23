<?php

namespace AwsLightsailBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 实例状态枚举
 *
 * @see https://docs.aws.amazon.com/lightsail/2016-11-28/api-reference/API_InstanceState.html AWS Lightsail 实例状态 API 文档
 */
enum InstanceStateEnum: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case PENDING = 'pending';
    case RUNNING = 'running';
    case STOPPING = 'stopping';
    case STOPPED = 'stopped';
    case REBOOTING = 'rebooting';
    case DELETING = 'deleting';
    case ERROR = 'error';
    case UNKNOWN = 'unknown';

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => '处理中',
            self::RUNNING => '运行中',
            self::STOPPING => '停止中',
            self::STOPPED => '已停止',
            self::REBOOTING => '重启中',
            self::DELETING => '删除中',
            self::ERROR => '错误',
            self::UNKNOWN => '未知',
        };
    }

    /**
     * 从字符串状态转换为对应的枚举值
     */
    public static function fromString(string $state): self
    {
        return match ($state) {
            'pending' => self::PENDING,
            'running' => self::RUNNING,
            'stopping' => self::STOPPING,
            'stopped' => self::STOPPED,
            'rebooting' => self::REBOOTING,
            'deleting' => self::DELETING,
            'error' => self::ERROR,
            default => self::UNKNOWN,
        };
    }
}
