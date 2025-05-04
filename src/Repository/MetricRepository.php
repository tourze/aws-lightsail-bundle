<?php

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Client\LightsailApiClient;
use AwsLightsailBundle\Entity\Metric;
use AwsLightsailBundle\Request\GetInstanceMetricDataRequest;
use Psr\Log\LoggerInterface;

/**
 * AWS Lightsail 指标仓库
 *
 * @method Metric|null findOneBy(array $criteria)
 * @method Metric[] findAll()
 * @method Metric[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MetricRepository
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
     * @param int $period 周期（秒）
     * @param int $startTime 开始时间（UNIX时间戳）
     * @param int $endTime 结束时间（UNIX时间戳）
     * @param string $unit 单位
     * @param array $statistics 统计方式数组
     * @param string $accessKey AWS 访问密钥
     * @param string $secretKey AWS 密钥
     * @param string $region AWS 区域
     * @return Metric|null 指标数据或null（出错）
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
    ): ?Metric {
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
            $response = $this->lightsailApiClient->request($request);
            return Metric::fromApiResponse($response);
        } catch (\Exception $e) {
            $this->logger->error('获取实例指标数据失败', [
                'exception' => $e,
                'instanceName' => $instanceName,
                'metricName' => $metricName,
                'region' => $region
            ]);
            return null;
        }
    }
}
