<?php

namespace AwsLightsailBundle\Service;

use AwsLightsailBundle\Client\LightsailApiClient;
use AwsLightsailBundle\Request\CreateInstanceRequest;
use AwsLightsailBundle\Request\CreateInstancesFromSnapshotRequest;
use AwsLightsailBundle\Request\DeleteInstanceRequest;
use AwsLightsailBundle\Request\GetInstanceRequest;
use AwsLightsailBundle\Request\GetInstanceStateRequest;
use AwsLightsailBundle\Request\StartInstanceRequest;
use AwsLightsailBundle\Request\StopInstanceRequest;
use Psr\Log\LoggerInterface;

/**
 * AWS Lightsail 实例服务
 */
class InstanceService
{
    public function __construct(
        private readonly LightsailApiClient $lightsailApiClient,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * 创建实例
     *
     * @param string $instanceName 实例名称
     * @param string $availabilityZone 可用区
     * @param string $blueprintId 系统镜像ID
     * @param string $bundleId 实例规格
     * @param string $keyPairName SSH密钥名称
     * @param string $accessKey AWS访问密钥
     * @param string $secretKey AWS秘密密钥
     * @param string $region AWS区域
     * @return array 创建结果
     */
    public function createInstance(
        string $instanceName,
        string $availabilityZone,
        string $blueprintId,
        string $bundleId,
        string $keyPairName,
        string $accessKey,
        string $secretKey,
        string $region
    ): array {
        $this->logger->info('创建实例', [
            'instanceName' => $instanceName,
            'availabilityZone' => $availabilityZone,
            'blueprintId' => $blueprintId,
            'bundleId' => $bundleId,
            'region' => $region
        ]);

        $request = new CreateInstanceRequest(
            [$instanceName],
            $availabilityZone,
            $blueprintId,
            $bundleId,
            $keyPairName
        );

        $request->setCredentials($accessKey, $secretKey, $region);

        try {
            $response = $this->lightsailApiClient->request($request);
            $this->logger->info('成功创建实例', [
                'response' => $response
            ]);
            return $response;
        } catch (\Exception $e) {
            $this->logger->error('创建实例失败', [
                'exception' => $e,
                'instanceName' => $instanceName
            ]);
            throw $e;
        }
    }

    /**
     * 获取实例信息
     *
     * @param string $instanceName 实例名称
     * @param string $accessKey AWS访问密钥
     * @param string $secretKey AWS秘密密钥
     * @param string $region AWS区域
     * @return array 实例信息
     */
    public function getInstance(
        string $instanceName,
        string $accessKey,
        string $secretKey,
        string $region
    ): array {
        $request = new GetInstanceRequest($instanceName);
        $request->setCredentials($accessKey, $secretKey, $region);

        try {
            return $this->lightsailApiClient->request($request);
        } catch (\Exception $e) {
            $this->logger->error('获取实例信息失败', [
                'exception' => $e,
                'instanceName' => $instanceName
            ]);
            throw $e;
        }
    }

    /**
     * 获取实例状态
     *
     * @param string $instanceName 实例名称
     * @param string $accessKey AWS访问密钥
     * @param string $secretKey AWS秘密密钥
     * @param string $region AWS区域
     * @return array 实例状态
     */
    public function getInstanceState(
        string $instanceName,
        string $accessKey,
        string $secretKey,
        string $region
    ): array {
        $request = new GetInstanceStateRequest($instanceName);
        $request->setCredentials($accessKey, $secretKey, $region);

        try {
            return $this->lightsailApiClient->request($request);
        } catch (\Exception $e) {
            $this->logger->error('获取实例状态失败', [
                'exception' => $e,
                'instanceName' => $instanceName
            ]);
            throw $e;
        }
    }

    /**
     * 停止实例
     *
     * @param string $instanceName 实例名称
     * @param bool $force 是否强制停止
     * @param string $accessKey AWS访问密钥
     * @param string $secretKey AWS秘密密钥
     * @param string $region AWS区域
     * @return array 结果
     */
    public function stopInstance(
        string $instanceName,
        bool $force,
        string $accessKey,
        string $secretKey,
        string $region
    ): array {
        $request = new StopInstanceRequest($instanceName, $force);
        $request->setCredentials($accessKey, $secretKey, $region);

        try {
            return $this->lightsailApiClient->request($request);
        } catch (\Exception $e) {
            $this->logger->error('停止实例失败', [
                'exception' => $e,
                'instanceName' => $instanceName
            ]);
            throw $e;
        }
    }

    /**
     * 启动实例
     *
     * @param string $instanceName 实例名称
     * @param string $accessKey AWS访问密钥
     * @param string $secretKey AWS秘密密钥
     * @param string $region AWS区域
     * @return array 结果
     */
    public function startInstance(
        string $instanceName,
        string $accessKey,
        string $secretKey,
        string $region
    ): array {
        $request = new StartInstanceRequest($instanceName);
        $request->setCredentials($accessKey, $secretKey, $region);

        try {
            return $this->lightsailApiClient->request($request);
        } catch (\Exception $e) {
            $this->logger->error('启动实例失败', [
                'exception' => $e,
                'instanceName' => $instanceName
            ]);
            throw $e;
        }
    }

    /**
     * 删除实例
     *
     * @param string $instanceName 实例名称
     * @param bool $forceDeleteAddOns 是否强制删除附加组件
     * @param string $accessKey AWS访问密钥
     * @param string $secretKey AWS秘密密钥
     * @param string $region AWS区域
     * @return array 结果
     */
    public function deleteInstance(
        string $instanceName,
        bool $forceDeleteAddOns,
        string $accessKey,
        string $secretKey,
        string $region
    ): array {
        $request = new DeleteInstanceRequest($instanceName, $forceDeleteAddOns);
        $request->setCredentials($accessKey, $secretKey, $region);

        try {
            return $this->lightsailApiClient->request($request);
        } catch (\Exception $e) {
            $this->logger->error('删除实例失败', [
                'exception' => $e,
                'instanceName' => $instanceName
            ]);
            throw $e;
        }
    }

    /**
     * 从快照创建实例
     *
     * @param array $instanceNames 实例名称列表
     * @param string $availabilityZone 可用区
     * @param string $instanceSnapshotName 快照名称
     * @param string $bundleId 实例规格
     * @param string $accessKey AWS访问密钥
     * @param string $secretKey AWS秘密密钥
     * @param string $region AWS区域
     * @return array 创建结果
     */
    public function createInstancesFromSnapshot(
        array $instanceNames,
        string $availabilityZone,
        string $instanceSnapshotName,
        string $bundleId,
        string $accessKey,
        string $secretKey,
        string $region
    ): array {
        $request = new CreateInstancesFromSnapshotRequest(
            $instanceNames,
            $availabilityZone,
            $instanceSnapshotName,
            $bundleId
        );
        $request->setCredentials($accessKey, $secretKey, $region);

        try {
            return $this->lightsailApiClient->request($request);
        } catch (\Exception $e) {
            $this->logger->error('从快照创建实例失败', [
                'exception' => $e,
                'instanceNames' => $instanceNames,
                'instanceSnapshotName' => $instanceSnapshotName
            ]);
            throw $e;
        }
    }
}
