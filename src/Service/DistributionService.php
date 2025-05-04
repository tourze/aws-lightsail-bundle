<?php

namespace AwsLightsailBundle\Service;

use AwsLightsailBundle\Client\LightsailApiClient;
use AwsLightsailBundle\Request\CreateDistributionRequest;
use AwsLightsailBundle\Request\DeleteDistributionRequest;
use AwsLightsailBundle\Request\GetDistributionRequest;
use AwsLightsailBundle\Request\GetDistributionsRequest;
use AwsLightsailBundle\Request\UpdateDistributionRequest;
use Psr\Log\LoggerInterface;

/**
 * 静态资源分发服务类
 *
 * 该类负责AWS Lightsail静态资源分发相关操作
 */
class DistributionService
{
    public function __construct(
        private readonly LightsailApiClient $lightsailApiClient,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * 创建静态资源分发
     */
    public function createDistribution(
        string $distributionName,
        string $bundleId,
        array $origin,
        array $defaultCacheBehavior,
        array $cacheBehaviors,
        bool $enableDistribution,
        array $tags,
        string $accessKey,
        string $secretKey,
        string $region
    ): array {
        $this->logger->info('创建静态资源分发', [
            'distributionName' => $distributionName,
            'bundleId' => $bundleId,
            'region' => $region
        ]);

        $request = new CreateDistributionRequest(
            $distributionName,
            $bundleId,
            $origin,
            $defaultCacheBehavior,
            $cacheBehaviors,
            $enableDistribution,
            $tags
        );
        $request->setCredentials($accessKey, $secretKey, $region);

        try {
            $response = $this->lightsailApiClient->request($request);
            $this->logger->info('成功创建静态资源分发', [
                'response' => $response
            ]);
            return $response;
        } catch (\Exception $e) {
            $this->logger->error('创建静态资源分发失败', [
                'exception' => $e,
                'distributionName' => $distributionName
            ]);
            throw $e;
        }
    }

    /**
     * 获取静态资源分发
     */
    public function getDistribution(
        string $distributionName,
        string $accessKey,
        string $secretKey,
        string $region
    ): array {
        $request = new GetDistributionRequest($distributionName);
        $request->setCredentials($accessKey, $secretKey, $region);

        try {
            return $this->lightsailApiClient->request($request);
        } catch (\Exception $e) {
            $this->logger->error('获取静态资源分发失败', [
                'exception' => $e,
                'distributionName' => $distributionName
            ]);
            throw $e;
        }
    }

    /**
     * 获取静态资源分发列表
     */
    public function getDistributions(
        string $accessKey,
        string $secretKey,
        string $region
    ): array {
        $request = new GetDistributionsRequest();
        $request->setCredentials($accessKey, $secretKey, $region);

        try {
            return $this->lightsailApiClient->request($request);
        } catch (\Exception $e) {
            $this->logger->error('获取静态资源分发列表失败', [
                'exception' => $e
            ]);
            throw $e;
        }
    }

    /**
     * 更新静态资源分发
     */
    public function updateDistribution(
        string $distributionName,
        bool $isEnabled,
        array $defaultCacheBehavior,
        array $cacheBehaviorSettings,
        array $origin,
        string $accessKey,
        string $secretKey,
        string $region
    ): array {
        $this->logger->info('更新静态资源分发', [
            'distributionName' => $distributionName,
            'region' => $region
        ]);

        $request = new UpdateDistributionRequest(
            $distributionName,
            $isEnabled,
            $defaultCacheBehavior,
            $cacheBehaviorSettings,
            $origin
        );
        $request->setCredentials($accessKey, $secretKey, $region);

        try {
            $response = $this->lightsailApiClient->request($request);
            $this->logger->info('成功更新静态资源分发', [
                'response' => $response
            ]);
            return $response;
        } catch (\Exception $e) {
            $this->logger->error('更新静态资源分发失败', [
                'exception' => $e,
                'distributionName' => $distributionName
            ]);
            throw $e;
        }
    }

    /**
     * 删除静态资源分发
     */
    public function deleteDistribution(
        string $distributionName,
        string $accessKey,
        string $secretKey,
        string $region
    ): array {
        $request = new DeleteDistributionRequest($distributionName);
        $request->setCredentials($accessKey, $secretKey, $region);

        try {
            return $this->lightsailApiClient->request($request);
        } catch (\Exception $e) {
            $this->logger->error('删除静态资源分发失败', [
                'exception' => $e,
                'distributionName' => $distributionName
            ]);
            throw $e;
        }
    }
} 