<?php

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Client\LightsailApiClient;
use AwsLightsailBundle\Entity\Instance;
use AwsLightsailBundle\Request\GetInstanceRequest;
use AwsLightsailBundle\Request\GetInstanceStateRequest;
use Psr\Log\LoggerInterface;

/**
 * AWS Lightsail 实例仓库
 *
 * @method Instance|null findOneBy(array $criteria)
 * @method Instance[] findAll()
 * @method Instance[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InstanceRepository
{
    public function __construct(
        private readonly LightsailApiClient $lightsailApiClient,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * 获取单个实例
     *
     * @param string $instanceName 实例名称
     * @param string $accessKey AWS 访问密钥
     * @param string $secretKey AWS 密钥
     * @param string $region AWS 区域
     * @return Instance|null 实例对象或null（找不到或出错）
     */
    public function findByName(
        string $instanceName,
        string $accessKey,
        string $secretKey,
        string $region
    ): ?Instance {
        $request = new GetInstanceRequest($instanceName);
        $request->setCredentials($accessKey, $secretKey, $region);

        try {
            $response = $this->lightsailApiClient->request($request);
            return Instance::fromApiResponse($response);
        } catch (\Exception $e) {
            $this->logger->error('获取实例失败', [
                'exception' => $e,
                'instanceName' => $instanceName,
                'region' => $region
            ]);
            return null;
        }
    }

    /**
     * 获取实例状态
     *
     * @param string $instanceName 实例名称
     * @param string $accessKey AWS 访问密钥
     * @param string $secretKey AWS 密钥
     * @param string $region AWS 区域
     * @return array|null 实例状态数据或null（出错）
     */
    public function getInstanceState(
        string $instanceName,
        string $accessKey,
        string $secretKey,
        string $region
    ): ?array {
        $request = new GetInstanceStateRequest($instanceName);
        $request->setCredentials($accessKey, $secretKey, $region);

        try {
            return $this->lightsailApiClient->request($request);
        } catch (\Exception $e) {
            $this->logger->error('获取实例状态失败', [
                'exception' => $e,
                'instanceName' => $instanceName,
                'region' => $region
            ]);
            return null;
        }
    }
}
