<?php

namespace AwsLightsailBundle\Service;

use AwsLightsailBundle\Client\LightsailApiClient;
use AwsLightsailBundle\Request\CreateKeyPairRequest;
use AwsLightsailBundle\Request\GetKeyPairsRequest;
use Psr\Log\LoggerInterface;

/**
 * AWS Lightsail SSH密钥对服务
 */
class KeyPairService
{
    public function __construct(
        private readonly LightsailApiClient $lightsailApiClient,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * 获取SSH密钥对
     *
     * @param bool $includeDefaultKeyPair 是否包含默认密钥对
     * @param string $accessKey AWS访问密钥
     * @param string $secretKey AWS秘密密钥
     * @param string $region AWS区域
     * @return array 密钥对信息
     */
    public function getKeyPairs(
        bool $includeDefaultKeyPair,
        string $accessKey,
        string $secretKey,
        string $region
    ): array {
        $request = new GetKeyPairsRequest($includeDefaultKeyPair);
        $request->setCredentials($accessKey, $secretKey, $region);

        try {
            return $this->lightsailApiClient->request($request);
        } catch (\Exception $e) {
            $this->logger->error('获取SSH密钥对失败', [
                'exception' => $e
            ]);
            throw $e;
        }
    }

    /**
     * 创建SSH密钥对
     *
     * @param string $keyPairName 密钥对名称
     * @param string $accessKey AWS访问密钥
     * @param string $secretKey AWS秘密密钥
     * @param string $region AWS区域
     * @return array 创建结果
     */
    public function createKeyPair(
        string $keyPairName,
        string $accessKey,
        string $secretKey,
        string $region
    ): array {
        $request = new CreateKeyPairRequest($keyPairName);
        $request->setCredentials($accessKey, $secretKey, $region);

        try {
            return $this->lightsailApiClient->request($request);
        } catch (\Exception $e) {
            $this->logger->error('创建SSH密钥对失败', [
                'exception' => $e,
                'keyPairName' => $keyPairName
            ]);
            throw $e;
        }
    }
} 