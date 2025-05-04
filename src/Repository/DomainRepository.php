<?php

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Client\LightsailApiClient;
use AwsLightsailBundle\Entity\Domain;
use AwsLightsailBundle\Request\GetDomainRequest;
use AwsLightsailBundle\Request\GetDomainsRequest;
use Psr\Log\LoggerInterface;

/**
 * AWS Lightsail 域名仓库
 *
 * @method Domain|null findOneBy(array $criteria)
 * @method Domain[] findAll()
 * @method Domain[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DomainRepository
{
    public function __construct(
        private readonly LightsailApiClient $lightsailApiClient,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * 获取所有域名
     *
     * @param string $accessKey AWS 访问密钥
     * @param string $secretKey AWS 密钥
     * @param string $region AWS 区域
     * @return array 域名数组
     */
    public function findAll(
        string $accessKey,
        string $secretKey,
        string $region
    ): array {
        $request = new GetDomainsRequest();
        $request->setCredentials($accessKey, $secretKey, $region);

        try {
            $response = $this->lightsailApiClient->request($request);
            $domains = [];

            if (isset($response['domains']) && is_array($response['domains'])) {
                foreach ($response['domains'] as $domainData) {
                    $domains[] = Domain::fromApiResponse($domainData);
                }
            }

            return $domains;
        } catch (\Exception $e) {
            $this->logger->error('获取域名列表失败', [
                'exception' => $e,
                'region' => $region
            ]);
            return [];
        }
    }

    /**
     * 根据名称查找域名
     *
     * @param string $domainName 域名名称
     * @param string $accessKey AWS 访问密钥
     * @param string $secretKey AWS 密钥
     * @param string $region AWS 区域
     * @return Domain|null 域名实体或null（找不到或出错）
     */
    public function findByName(
        string $domainName,
        string $accessKey,
        string $secretKey,
        string $region
    ): ?Domain {
        $request = new GetDomainRequest($domainName);
        $request->setCredentials($accessKey, $secretKey, $region);

        try {
            $response = $this->lightsailApiClient->request($request);
            return Domain::fromApiResponse($response);
        } catch (\Exception $e) {
            $this->logger->error('获取域名失败', [
                'exception' => $e,
                'domainName' => $domainName,
                'region' => $region
            ]);
            return null;
        }
    }
}
