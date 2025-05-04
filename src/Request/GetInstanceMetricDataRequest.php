<?php

namespace AwsLightsailBundle\Request;

/**
 * 获取Lightsail实例指标数据的请求
 * @see https://docs.aws.amazon.com/lightsail/2016-11-28/api-reference/API_GetInstanceMetricData.html
 */
class GetInstanceMetricDataRequest extends LightsailRequest
{
    /**
     * @param string $instanceName 实例名称
     * @param string $metricName 指标名称
     * @param int $period 时间周期
     * @param int $startTime 开始时间
     * @param int $endTime 结束时间
     * @param string $unit 单位
     * @param array $statistics 统计方式
     */
    public function __construct(
        private readonly string $instanceName,
        private readonly string $metricName,
        private readonly int $period,
        private readonly int $startTime,
        private readonly int $endTime,
        private readonly string $unit,
        private readonly array $statistics
    ) {
    }

    /**
     * API 端点
     */
    public function getRequestPath(): string
    {
        return '/';
    }

    /**
     * 请求参数
     */
    public function getRequestOptions(): ?array
    {
        return [
            'json' => [
                'instanceName' => $this->instanceName,
                'metricName' => $this->metricName,
                'period' => $this->period,
                'startTime' => $this->startTime,
                'endTime' => $this->endTime,
                'unit' => $this->unit,
                'statistics' => $this->statistics
            ],
            'headers' => [
                'X-Amz-Target' => 'Lightsail_20161128.GetInstanceMetricData'
            ]
        ];
    }

    /**
     * 请求方法
     */
    public function getRequestMethod(): ?string
    {
        return 'POST';
    }
}
