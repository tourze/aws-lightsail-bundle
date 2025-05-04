<?php

namespace AwsLightsailBundle\Service;

use AwsLightsailBundle\Client\LightsailApiClient;
use AwsLightsailBundle\Request\CreateInstanceSnapshotRequest;
use Psr\Log\LoggerInterface;

/**
 * AWS Lightsail 实例快照服务
 */
class InstanceSnapshotService
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
     * @param string $snapshotName 快照名称
     * @param array<string, string> $tags 标签
     * @param string $accessKey AWS访问密钥
     * @param string $secretKey AWS秘密密钥
     * @param string $region AWS区域
     * @return array 快照创建结果
     */
    public function createInstanceSnapshot(
        string $instanceName,
        string $snapshotName,
        array $tags,
        string $accessKey,
        string $secretKey,
        string $region
    ): array {
        $this->logger->info('创建实例快照', [
            'instanceName' => $instanceName,
            'snapshotName' => $snapshotName,
            'tags' => $tags,
            'region' => $region
        ]);

        $request = new CreateInstanceSnapshotRequest(
            $instanceName,
            $snapshotName,
            $tags
        );

        // 设置凭证
        $request->setCredentials($accessKey, $secretKey, $region);

        try {
            $response = $this->lightsailApiClient->request($request);
            $this->logger->info('成功创建实例快照', [
                'response' => $response
            ]);
            return $response;
        } catch (\Exception $e) {
            $this->logger->error('创建实例快照失败', [
                'exception' => $e,
                'instanceName' => $instanceName,
                'snapshotName' => $snapshotName
            ]);
            throw $e;
        }
    }
}
