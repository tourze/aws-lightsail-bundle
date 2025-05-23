<?php

namespace AwsLightsailBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 告警指标枚举
 *
 * @see https://docs.aws.amazon.com/lightsail/2016-11-28/api-reference/API_MetricName.html AWS Lightsail 告警指标 API 文档
 */
enum AlarmMetricEnum: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    // 实例指标
    case CPU_UTILIZATION = 'CPUUtilization';
    case NETWORK_IN = 'NetworkIn';
    case NETWORK_OUT = 'NetworkOut';
    case STATUS_CHECK_FAILED = 'StatusCheckFailed';
    case DISK_READ_BYTES = 'DiskReadBytes';
    case DISK_WRITE_BYTES = 'DiskWriteBytes';
    case MEMORY_UTILIZATION = 'MemoryUtilization';

    // 数据库指标
    case DB_CONNECTIONS = 'DatabaseConnections';
    case DB_CPU_UTILIZATION = 'CPUUtilization';
    case DB_FREE_STORAGE_SPACE = 'FreeStorageSpace';
    case DB_NETWORK_RECEIVE_THROUGHPUT = 'NetworkReceiveThroughput';
    case DB_NETWORK_TRANSMIT_THROUGHPUT = 'NetworkTransmitThroughput';

    // 负载均衡器指标
    case LB_HEALTHY_HOST_COUNT = 'HealthyHostCount';
    case LB_UNHEALTHY_HOST_COUNT = 'UnhealthyHostCount';
    case LB_HTTP_4XX_COUNT = 'HTTPCode_LB_4XX_Count';
    case LB_HTTP_5XX_COUNT = 'HTTPCode_LB_5XX_Count';
    case LB_INSTANCE_HTTP_5XX_COUNT = 'HTTPCode_Instance_5XX_Count';

    // 存储桶指标
    case BUCKET_SIZE_BYTES = 'BucketSizeBytes';
    case NUMBER_OF_OBJECTS = 'NumberOfObjects';

    public function getLabel(): string
    {
        return match ($this) {
            self::CPU_UTILIZATION => 'CPU 使用率',
            self::NETWORK_IN => '网络入流量',
            self::NETWORK_OUT => '网络出流量',
            self::STATUS_CHECK_FAILED => '状态检查失败',
            self::DISK_READ_BYTES => '磁盘读取字节数',
            self::DISK_WRITE_BYTES => '磁盘写入字节数',
            self::MEMORY_UTILIZATION => '内存使用率',
            self::DB_CONNECTIONS => '数据库连接数',
            self::DB_CPU_UTILIZATION => '数据库 CPU 使用率',
            self::DB_FREE_STORAGE_SPACE => '数据库可用存储空间',
            self::DB_NETWORK_RECEIVE_THROUGHPUT => '数据库网络接收吞吐量',
            self::DB_NETWORK_TRANSMIT_THROUGHPUT => '数据库网络传输吞吐量',
            self::LB_HEALTHY_HOST_COUNT => '负载均衡器健康主机数',
            self::LB_UNHEALTHY_HOST_COUNT => '负载均衡器不健康主机数',
            self::LB_HTTP_4XX_COUNT => '负载均衡器 HTTP 4XX 错误数',
            self::LB_HTTP_5XX_COUNT => '负载均衡器 HTTP 5XX 错误数',
            self::LB_INSTANCE_HTTP_5XX_COUNT => '实例 HTTP 5XX 错误数',
            self::BUCKET_SIZE_BYTES => '存储桶大小(字节)',
            self::NUMBER_OF_OBJECTS => '对象数量',
        };
    }
}
