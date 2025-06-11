<?php

namespace AwsLightsailBundle\Service;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\KeyPair;
use AwsLightsailBundle\Repository\KeyPairRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * AWS Lightsail 密钥对同步服务
 */
class KeyPairSyncService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly KeyPairRepository $keyPairRepository,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * 从 AWS API 数据更新或创建密钥对
     *
     * @param AwsCredential $credential AWS 凭证
     * @param array $data AWS API 返回的密钥对数据
     * @param bool $flush 是否立即刷新到数据库，默认为 true
     * @return KeyPair 更新后的密钥对对象
     */
    public function updateKeyPairFromData(AwsCredential $credential, array $data, bool $flush = true): KeyPair
    {
        $keyPairName = $data['name'] ?? '';
        if (empty($keyPairName)) {
            throw new \InvalidArgumentException('密钥对名称不能为空');
        }

        // 从 location 获取区域信息
        $region = $data['location']['regionName'] ?? '';
        if (empty($region)) {
            throw new \InvalidArgumentException('密钥对区域不能为空');
        }

        // 查找是否已存在此密钥对
        $keyPair = $this->keyPairRepository->findOneByNameAndCredentialAndRegion($keyPairName, $credential, $region);

        // 如果不存在则创建新密钥对
        if (!$keyPair) {
            $keyPair = new KeyPair();
            $keyPair->setName($keyPairName);
            $keyPair->setCredential($credential);
            $keyPair->setRegion($region);
            $this->logger->info('创建新密钥对', ['name' => $keyPairName, 'credential' => $credential->getName(), 'region' => $region]);
        } else {
            $this->logger->debug('更新现有密钥对', ['name' => $keyPairName, 'credential' => $credential->getName(), 'region' => $region]);
        }

        // 更新基本信息
        if (isset($data['arn'])) {
            $keyPair->setArn($data['arn']);
        }

        if (isset($data['fingerprint'])) {
            $keyPair->setFingerprint($data['fingerprint']);
        }

        if (isset($data['resourceType'])) {
            $keyPair->setResourceType($data['resourceType']);
        }

        if (isset($data['supportCode'])) {
            $keyPair->setSupportCode($data['supportCode']);
        }

        // 更新标签
        if (isset($data['tags']) && is_array($data['tags'])) {
            $tags = [];
            foreach ($data['tags'] as $tag) {
                if (isset($tag['key']) && isset($tag['value'])) {
                    $tags[$tag['key']] = $tag['value'];
                }
            }
            $keyPair->setTags($tags);
        }

        // AWS 创建时间
        if (isset($data['createdAt'])) {
            try {
                if (is_numeric($data['createdAt'])) {
                    // Unix timestamp
                    $keyPair->setAwsCreatedAt(Carbon::createFromTimestamp('@' . $data['createdAt']));
                } elseif ($data['createdAt'] instanceof \DateTime) {
                    $keyPair->setAwsCreatedAt(Carbon::parse($data['createdAt']));
                } elseif ($data['createdAt'] instanceof \DateTimeImmutable) {
                    $keyPair->setAwsCreatedAt($data['createdAt']);
                } elseif (is_string($data['createdAt'])) {
                    $keyPair->setAwsCreatedAt(Carbon::parse($data['createdAt']));
                }
            } catch (\Throwable $e) {
                $this->logger->warning('无法解析 AWS 创建时间', [
                    'createdAt' => $data['createdAt'],
                    'error' => $e->getMessage()
                ]);
            }
        }

        // 设置同步时间
        $keyPair->setSyncTime(Carbon::now());

        // 保存密钥对到持久化上下文
        $this->entityManager->persist($keyPair);

        // 根据参数决定是否立即刷新
        if ($flush) {
            $this->entityManager->flush();
        }

        return $keyPair;
    }

    /**
     * 批量同步密钥对数据
     *
     * @param AwsCredential $credential AWS 凭证
     * @param array $keyPairsData AWS API 返回的密钥对数据数组
     * @return array 包含同步统计信息的数组
     */
    public function batchSyncKeyPairs(AwsCredential $credential, array $keyPairsData): array
    {
        $stats = [
            'total' => 0,
            'new' => 0,
            'updated' => 0,
            'errors' => 0,
        ];

        foreach ($keyPairsData as $keyPairData) {
            try {
                // 从数据中获取区域和名称
                $region = $keyPairData['location']['regionName'] ?? '';
                $keyPairName = $keyPairData['name'] ?? '';

                $existingId = null;
                if (!empty($keyPairName) && !empty($region)) {
                    $existing = $this->keyPairRepository->findOneByNameAndCredentialAndRegion($keyPairName, $credential, $region);
                    $existingId = $existing?->getId();
                }

                // 不立即刷新，等批量处理完成后统一刷新
                $keyPair = $this->updateKeyPairFromData($credential, $keyPairData, false);

                if ($existingId) {
                    $stats['updated']++;
                } else {
                    $stats['new']++;
                }
                $stats['total']++;
            } catch (\Throwable $e) {
                $stats['errors']++;
                $this->logger->error('同步密钥对时出错', [
                    'keyPairData' => $keyPairData,
                    'credential' => $credential->getName(),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // 批量处理完成后统一刷新到数据库
        try {
            $this->entityManager->flush();
        } catch (\Throwable $e) {
            $this->logger->error('批量刷新密钥对数据到数据库时出错', [
                'credential' => $credential->getName(),
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }

        return $stats;
    }

    /**
     * 根据名称和凭证查找密钥对
     */
    public function findKeyPairByNameAndCredentialAndRegion(string $name, AwsCredential $credential, string $region): ?KeyPair
    {
        return $this->keyPairRepository->findOneByNameAndCredentialAndRegion($name, $credential, $region);
    }

    /**
     * 清理远程已删除的密钥对
     *
     * @param AwsCredential $credential AWS 凭证
     * @param array $remoteKeyPairNames 远程存在的密钥对名称列表
     * @param string $region 区域
     * @return int 删除的密钥对数量
     */
    public function cleanupDeletedKeyPairs(AwsCredential $credential, array $remoteKeyPairNames, string $region): int
    {
        // 获取本地所有密钥对
        $localKeyPairs = $this->keyPairRepository->findBy([
            'credential' => $credential,
            'region' => $region,
        ]);

        $deletedCount = 0;
        foreach ($localKeyPairs as $localKeyPair) {
            if (!in_array($localKeyPair->getName(), $remoteKeyPairNames, true)) {
                $this->logger->info('删除远程已不存在的密钥对', [
                    'name' => $localKeyPair->getName(),
                    'credential' => $credential->getName(),
                    'region' => $region,
                ]);
                $this->entityManager->remove($localKeyPair);
                $deletedCount++;
            }
        }

        if ($deletedCount > 0) {
            $this->entityManager->flush();
        }

        return $deletedCount;
    }
}
