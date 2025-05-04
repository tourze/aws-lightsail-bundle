<?php

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Client\LightsailApiClient;
use AwsLightsailBundle\Entity\Network;
use AwsLightsailBundle\Request\OpenInstancePublicPortsRequest;
use Psr\Log\LoggerInterface;

/**
 * AWS Lightsail 网络仓库
 *
 * @method Network|null findOneBy(array $criteria)
 * @method Network[] findAll()
 * @method Network[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NetworkRepository
{
    public function __construct(
        private readonly LightsailApiClient $lightsailApiClient,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * 打开实例的公共端口
     *
     * @param string $instanceName 实例名称
     * @param string $protocol 协议
     * @param int $fromPort 起始端口
     * @param int $toPort 结束端口
     * @param string $accessKey AWS 访问密钥
     * @param string $secretKey AWS 密钥
     * @param string $region AWS 区域
     * @param array $cidrs CIDR列表
     * @param array $ipv6Cidrs IPv6 CIDR列表
     * @param array $cidrListAliases CIDR列表别名
     * @return array|null 操作结果或null（出错）
     */
    public function openInstancePublicPorts(
        string $instanceName,
        string $protocol,
        int $fromPort,
        int $toPort,
        string $accessKey,
        string $secretKey,
        string $region,
        array $cidrs = [],
        array $ipv6Cidrs = [],
        array $cidrListAliases = []
    ): ?array {
        $request = new OpenInstancePublicPortsRequest(
            $instanceName,
            $fromPort,
            $toPort,
            $protocol,
            $cidrs,
            $ipv6Cidrs,
            $cidrListAliases
        );
        $request->setCredentials($accessKey, $secretKey, $region);

        try {
            return $this->lightsailApiClient->request($request);
        } catch (\Exception $e) {
            $this->logger->error('打开实例公共端口失败', [
                'exception' => $e,
                'instanceName' => $instanceName,
                'protocol' => $protocol,
                'fromPort' => $fromPort,
                'toPort' => $toPort,
                'region' => $region
            ]);
            return null;
        }
    }
}
