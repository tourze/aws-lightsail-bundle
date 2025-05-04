<?php

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Client\LightsailApiClient;
use AwsLightsailBundle\Entity\InstanceSnapshot;
use AwsLightsailBundle\Request\GetInstanceSnapshotRequest;
use Psr\Log\LoggerInterface;

/**
 * AWS Lightsail 实例快照仓库
 *
 * @method InstanceSnapshot|null findOneBy(array $criteria)
 * @method InstanceSnapshot[] findAll()
 * @method InstanceSnapshot[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InstanceSnapshotRepository
{
    public function __construct(
        private readonly LightsailApiClient $lightsailApiClient,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * 根据名称查找快照
     *
     * @param string $snapshotName 快照名称
     * @param string $accessKey AWS 访问密钥
     * @param string $secretKey AWS 密钥
     * @param string $region AWS 区域
     * @return InstanceSnapshot|null 快照实体或null（找不到或出错）
     */
    public function findByName(
        string $snapshotName,
        string $accessKey,
        string $secretKey,
        string $region
    ): ?InstanceSnapshot {
        $request = new GetInstanceSnapshotRequest($snapshotName);
        $request->setCredentials($accessKey, $secretKey, $region);

        try {
            $response = $this->lightsailApiClient->request($request);
            return InstanceSnapshot::fromApiResponse($response);
        } catch (\Exception $e) {
            $this->logger->error('获取实例快照失败', [
                'exception' => $e,
                'snapshotName' => $snapshotName,
                'region' => $region
            ]);
            return null;
        }
    }
}
