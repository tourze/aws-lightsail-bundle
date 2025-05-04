<?php

namespace AwsLightsailBundle\Service;

use AwsLightsailBundle\Client\LightsailApiClient;
use AwsLightsailBundle\Request\OpenInstancePublicPortsRequest;
use Psr\Log\LoggerInterface;

/**
 * AWS Lightsail 网络服务
 */
class NetworkService
{
    public function __construct(
        private readonly LightsailApiClient $lightsailApiClient,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * 配置实例防火墙端口
     *
     * @param string $instanceName 实例名称
     * @param int $fromPort 起始端口
     * @param int $toPort 结束端口
     * @param string $protocol 协议
     * @param array $cidrs CIDR列表
     * @param array $ipv6Cidrs IPv6 CIDR列表
     * @param string $accessKey AWS访问密钥
     * @param string $secretKey AWS秘密密钥
     * @param string $region AWS区域
     * @return array 结果
     */
    public function openInstancePublicPorts(
        string $instanceName,
        int $fromPort,
        int $toPort,
        string $protocol,
        array $cidrs,
        array $ipv6Cidrs,
        string $accessKey,
        string $secretKey,
        string $region
    ): array {
        $request = new OpenInstancePublicPortsRequest(
            $instanceName,
            $fromPort,
            $toPort,
            $protocol,
            $cidrs,
            $ipv6Cidrs
        );
        $request->setCredentials($accessKey, $secretKey, $region);

        try {
            return $this->lightsailApiClient->request($request);
        } catch (\Exception $e) {
            $this->logger->error('配置实例防火墙失败', [
                'exception' => $e,
                'instanceName' => $instanceName
            ]);
            throw $e;
        }
    }
}
