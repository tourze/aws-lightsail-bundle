<?php

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Client\LightsailApiClient;
use AwsLightsailBundle\Entity\KeyPair;
use AwsLightsailBundle\Request\GetKeyPairsRequest;
use Psr\Log\LoggerInterface;

/**
 * AWS Lightsail 密钥对仓库
 *
 * @method KeyPair|null findOneBy(array $criteria)
 * @method KeyPair[] findAll()
 * @method KeyPair[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class KeyPairRepository
{
    public function __construct(
        private readonly LightsailApiClient $lightsailApiClient,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * 获取所有密钥对
     *
     * @param string $accessKey AWS 访问密钥
     * @param string $secretKey AWS 密钥
     * @param string $region AWS 区域
     * @return array 密钥对数组
     */
    public function findAllFromApi(
        string $accessKey,
        string $secretKey,
        string $region
    ): array {
        $request = new GetKeyPairsRequest();
        $request->setCredentials($accessKey, $secretKey, $region);

        try {
            $response = $this->lightsailApiClient->request($request);
            $keyPairs = [];

            if (isset($response['keyPairs']) && is_array($response['keyPairs'])) {
                foreach ($response['keyPairs'] as $keyPairData) {
                    $keyPairs[] = KeyPair::fromApiResponse($keyPairData);
                }
            }

            return $keyPairs;
        } catch (\Exception $e) {
            $this->logger->error('获取密钥对列表失败', [
                'exception' => $e,
                'region' => $region
            ]);
            return [];
        }
    }

    /**
     * 根据名称查找密钥对
     *
     * @param string $name 密钥对名称
     * @param string $accessKey AWS 访问密钥
     * @param string $secretKey AWS 密钥
     * @param string $region AWS 区域
     * @return KeyPair|null 密钥对实体或null（找不到或出错）
     */
    public function findByName(
        string $name,
        string $accessKey,
        string $secretKey,
        string $region
    ): ?KeyPair {
        try {
            $keyPairs = $this->findAllFromApi($accessKey, $secretKey, $region);

            foreach ($keyPairs as $keyPair) {
                if ($keyPair->getName() === $name) {
                    return $keyPair;
                }
            }

            return null;
        } catch (\Exception $e) {
            $this->logger->error('查找密钥对失败', [
                'exception' => $e,
                'name' => $name,
                'region' => $region
            ]);
            return null;
        }
    }
}
