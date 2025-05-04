<?php

namespace AwsLightsailBundle\Service;

use AwsLightsailBundle\Client\LightsailApiClient;
use AwsLightsailBundle\Request\GetInstanceMetricDataRequest;
use Psr\Log\LoggerInterface;

/**
 * AWS Lightsail 监控指标服务
 */
class MetricsService
{
    public function __construct(
        private readonly LightsailApiClient $lightsailApiClient,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * 获取实例指标数据
     *
     * @param string $instanceName 实例名称
     * @param string $metricName 指标名称
     * @param int $period 时间周期
     * @param int $startTime 开始时间
     * @param int $endTime 结束时间
     * @param string $unit 单位
     * @param array $statistics 统计方式
     * @param string $accessKey AWS访问密钥
     * @param string $secretKey AWS秘密密钥
     * @param string $region AWS区域
     * @return array 指标数据
     */
    public function getInstanceMetricData(
        string $instanceName,
        string $metricName,
        int $period,
        int $startTime,
        int $endTime,
        string $unit,
        array $statistics,
        string $accessKey,
        string $secretKey,
        string $region
    ): array {
        $request = new GetInstanceMetricDataRequest(
            $instanceName,
            $metricName,
            $period,
            $startTime,
            $endTime,
            $unit,
            $statistics
        );
        $request->setCredentials($accessKey, $secretKey, $region);

        try {
            return $this->lightsailApiClient->request($request);
        } catch (\Exception $e) {
            $this->logger->error('获取实例指标数据失败', [
                'exception' => $e,
                'instanceName' => $instanceName,
                'metricName' => $metricName
            ]);
            throw $e;
        }
    }
} 