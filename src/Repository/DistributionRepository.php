<?php

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Client\LightsailApiClient;
use AwsLightsailBundle\Entity\Distribution;
use AwsLightsailBundle\Request\GetDistributionRequest;
use AwsLightsailBundle\Request\GetDistributionsRequest;
use Psr\Log\LoggerInterface;

/**
 * AWS Lightsail 分发仓库
 *
 * @method Distribution|null findOneBy(array $criteria)
 * @method Distribution[] findAll()
 * @method Distribution[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DistributionRepository
{
    public function __construct(
        private readonly LightsailApiClient $lightsailApiClient,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * 获取所有分发
     *
     * @param string $accessKey AWS 访问密钥
     * @param string $secretKey AWS 密钥
     * @param string $region AWS 区域
     * @return array 分发数组
     */
    public function findAll(
        string $accessKey,
        string $secretKey,
        string $region
    ): array {
        $request = new GetDistributionsRequest();
        $request->setCredentials($accessKey, $secretKey, $region);

        try {
            $response = $this->lightsailApiClient->request($request);
            $distributions = [];

            if (isset($response['distributions']) && is_array($response['distributions'])) {
                foreach ($response['distributions'] as $distributionData) {
                    $distributions[] = Distribution::fromApiResponse($distributionData);
                }
            }

            return $distributions;
        } catch (\Exception $e) {
            $this->logger->error('获取分发列表失败', [
                'exception' => $e,
                'region' => $region
            ]);
            return [];
        }
    }

    /**
     * 根据名称查找分发
     *
     * @param string $distributionName 分发名称
     * @param string $accessKey AWS 访问密钥
     * @param string $secretKey AWS 密钥
     * @param string $region AWS 区域
     * @return Distribution|null 分发实体或null（找不到或出错）
     */
    public function findByName(
        string $distributionName,
        string $accessKey,
        string $secretKey,
        string $region
    ): ?Distribution {
        $request = new GetDistributionRequest($distributionName);
        $request->setCredentials($accessKey, $secretKey, $region);

        try {
            $response = $this->lightsailApiClient->request($request);
            return Distribution::fromApiResponse($response);
        } catch (\Exception $e) {
            $this->logger->error('获取分发失败', [
                'exception' => $e,
                'distributionName' => $distributionName,
                'region' => $region
            ]);
            return null;
        }
    }
}
