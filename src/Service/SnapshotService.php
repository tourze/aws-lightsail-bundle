<?php

namespace AwsLightsailBundle\Service;

use AwsLightsailBundle\Client\LightsailApiClient;
use AwsLightsailBundle\Request\CreateInstanceSnapshotRequest;
use AwsLightsailBundle\Request\DeleteInstanceSnapshotRequest;
use AwsLightsailBundle\Request\GetInstanceSnapshotRequest;
use Psr\Log\LoggerInterface;

/**
 * AWS Lightsail 快照服务
 */
class SnapshotService
{
    public function __construct(
        private readonly LightsailApiClient $lightsailApiClient,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * 创建实例快照
     *
     * @param string $instanceName 实例名称
     * @param string $instanceSnapshotName 快照名称
     * @param array $tags 标签
     * @param string $accessKey AWS访问密钥
     * @param string $secretKey AWS秘密密钥
     * @param string $region AWS区域
     * @return array 创建结果
     */
    public function createInstanceSnapshot(
        string $instanceName,
        string $instanceSnapshotName,
        array $tags,
        string $accessKey,
        string $secretKey,
        string $region
    ): array {
        $request = new CreateInstanceSnapshotRequest(
            $instanceName,
            $instanceSnapshotName,
            $tags
        );
        $request->setCredentials($accessKey, $secretKey, $region);

        try {
            return $this->lightsailApiClient->request($request);
        } catch (\Exception $e) {
            $this->logger->error('创建实例快照失败', [
                'exception' => $e,
                'instanceName' => $instanceName,
                'instanceSnapshotName' => $instanceSnapshotName
            ]);
            throw $e;
        }
    }

    /**
     * 获取实例快照
     *
     * @param string $instanceSnapshotName 快照名称
     * @param string $accessKey AWS访问密钥
     * @param string $secretKey AWS秘密密钥
     * @param string $region AWS区域
     * @return array 快照信息
     */
    public function getInstanceSnapshot(
        string $instanceSnapshotName,
        string $accessKey,
        string $secretKey,
        string $region
    ): array {
        $request = new GetInstanceSnapshotRequest($instanceSnapshotName);
        $request->setCredentials($accessKey, $secretKey, $region);

        try {
            return $this->lightsailApiClient->request($request);
        } catch (\Exception $e) {
            $this->logger->error('获取实例快照失败', [
                'exception' => $e,
                'instanceSnapshotName' => $instanceSnapshotName
            ]);
            throw $e;
        }
    }

    /**
     * 删除实例快照
     *
     * @param string $instanceSnapshotName 快照名称
     * @param string $accessKey AWS访问密钥
     * @param string $secretKey AWS秘密密钥
     * @param string $region AWS区域
     * @return array 结果
     */
    public function deleteInstanceSnapshot(
        string $instanceSnapshotName,
        string $accessKey,
        string $secretKey,
        string $region
    ): array {
        $request = new DeleteInstanceSnapshotRequest($instanceSnapshotName);
        $request->setCredentials($accessKey, $secretKey, $region);

        try {
            return $this->lightsailApiClient->request($request);
        } catch (\Exception $e) {
            $this->logger->error('删除实例快照失败', [
                'exception' => $e,
                'instanceSnapshotName' => $instanceSnapshotName
            ]);
            throw $e;
        }
    }
}
