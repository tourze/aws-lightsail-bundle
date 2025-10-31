<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Service;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\KeyPair;
use AwsLightsailBundle\Exception\InvalidKeyPairDataException;
use AwsLightsailBundle\Repository\KeyPairRepository;
use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;

/**
 * AWS Lightsail 密钥对同步服务
 */
#[WithMonologChannel(channel: 'aws_lightsail')]
readonly class KeyPairSyncService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private KeyPairRepository $keyPairRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * 从 AWS API 数据更新或创建密钥对
     *
     * @param AwsCredential           $credential AWS 凭证
     * @param array<string, mixed>    $data       AWS API 返回的密钥对数据
     * @param bool                    $flush      是否立即刷新到数据库，默认为 true
     *
     * @return KeyPair 更新后的密钥对对象
     */
    public function updateKeyPairFromData(AwsCredential $credential, array $data, bool $flush = true): KeyPair
    {
        $keyPairName = $this->validateAndGetName($data);
        $region      = $this->validateAndGetRegion($data);

        $keyPair = $this->findOrCreateKeyPair($keyPairName, $credential, $region);

        $this->updateKeyPairBasicInfo($keyPair, $data);
        $this->updateKeyPairTags($keyPair, $data);
        $this->updateKeyPairCreateTime($keyPair, $data);

        $keyPair->setSyncTime(CarbonImmutable::now());
        $this->entityManager->persist($keyPair);

        if ($flush) {
            $this->entityManager->flush();
        }

        return $keyPair;
    }

    /**
     * 验证并获取密钥对名称
     *
     * @param array<string, mixed> $data AWS API 返回的数据
     *
     * @return string
     */
    private function validateAndGetName(array $data): string
    {
        $keyPairName = $data['name'] ?? '';
        if (!\is_string($keyPairName) || '' === $keyPairName) {
            throw new InvalidKeyPairDataException('密钥对名称不能为空');
        }

        return $keyPairName;
    }

    /**
     * 验证并获取区域
     *
     * @param array<string, mixed> $data AWS API 返回的数据
     *
     * @return string
     */
    private function validateAndGetRegion(array $data): string
    {
        $locationData = $data['location'] ?? [];
        \assert(\is_array($locationData), 'location data must be an array');
        $region = $locationData['regionName'] ?? '';
        if (!\is_string($region) || '' === $region) {
            throw new InvalidKeyPairDataException('密钥对区域不能为空');
        }

        return $region;
    }

    private function findOrCreateKeyPair(string $keyPairName, AwsCredential $credential, string $region): KeyPair
    {
        $keyPair = $this->keyPairRepository->findOneByNameAndCredentialAndRegion($keyPairName, $credential, $region);

        if (null === $keyPair) {
            $keyPair = new KeyPair();
            $keyPair->setName($keyPairName);
            $keyPair->setCredential($credential);
            $keyPair->setRegion($region);
            $this->logger->info('创建新密钥对', ['name' => $keyPairName, 'credential' => $credential->getName(), 'region' => $region]);
        } else {
            $this->logger->debug('更新现有密钥对', ['name' => $keyPairName, 'credential' => $credential->getName(), 'region' => $region]);
        }

        return $keyPair;
    }

    /**
     * 更新密钥对基本信息
     *
     * @param KeyPair                  $keyPair
     * @param array<string, mixed>     $data    AWS API 返回的数据
     */
    private function updateKeyPairBasicInfo(KeyPair $keyPair, array $data): void
    {
        if (isset($data['arn']) && \is_string($data['arn'])) {
            $keyPair->setArn($data['arn']);
        }

        if (isset($data['fingerprint']) && \is_string($data['fingerprint'])) {
            $keyPair->setFingerprint($data['fingerprint']);
        }

        if (isset($data['resourceType']) && \is_string($data['resourceType'])) {
            $keyPair->setResourceType($data['resourceType']);
        }

        if (isset($data['supportCode']) && \is_string($data['supportCode'])) {
            $keyPair->setSupportCode($data['supportCode']);
        }
    }

    /**
     * 更新密钥对标签
     *
     * @param KeyPair                  $keyPair
     * @param array<string, mixed>     $data    AWS API 返回的数据
     */
    private function updateKeyPairTags(KeyPair $keyPair, array $data): void
    {
        if (!isset($data['tags']) || !\is_array($data['tags'])) {
            return;
        }

        $tags = [];
        foreach ($data['tags'] as $tag) {
            if (\is_array($tag) && isset($tag['key'], $tag['value'])
                                && \is_string($tag['key']) && \is_string($tag['value'])) {
                $key        = $tag['key'];
                $value      = $tag['value'];
                $tags[$key] = $value;
            }
        }
        $keyPair->setTags($tags);
    }

    /**
     * 更新密钥对创建时间
     *
     * @param KeyPair                  $keyPair
     * @param array<string, mixed>     $data    AWS API 返回的数据
     */
    private function updateKeyPairCreateTime(KeyPair $keyPair, array $data): void
    {
        if (!isset($data['createdAt'])) {
            return;
        }

        try {
            $createdAt = $this->parseCreatedAt($data['createdAt']);
            if (null !== $createdAt) {
                $keyPair->setAwsCreateTime($createdAt);
            }
        } catch (\Throwable $e) {
            $this->logger->warning('无法解析 AWS 创建时间', [
                'createdAt' => $data['createdAt'],
                'error'     => $e->getMessage(),
            ]);
        }
    }

    /**
     * 解析创建时间
     *
     * @param mixed $createdAt 创建时间数据
     *
     * @return \DateTimeImmutable|null
     */
    private function parseCreatedAt($createdAt): ?\DateTimeImmutable
    {
        if (\is_numeric($createdAt)) {
            return CarbonImmutable::createFromTimestamp('@' . $createdAt);
        }

        if ($createdAt instanceof \DateTime) {
            return CarbonImmutable::parse($createdAt);
        }

        if ($createdAt instanceof \DateTimeImmutable) {
            return $createdAt;
        }

        if (\is_string($createdAt)) {
            return CarbonImmutable::parse($createdAt);
        }

        return null;
    }

    /**
     * 批量同步密钥对数据
     *
     * @param AwsCredential                           $credential   AWS 凭证
     * @param array<int, array<string, mixed>>        $keyPairsData AWS API 返回的密钥对数据数组
     *
     * @return array<string, int> 包含同步统计信息的数组
     */
    public function batchSyncKeyPairs(AwsCredential $credential, array $keyPairsData): array
    {
        $stats = [
            'total'   => 0,
            'new'     => 0,
            'updated' => 0,
            'errors'  => 0,
        ];

        foreach ($keyPairsData as $keyPairData) {
            try {
                // 从数据中获取区域和名称
                $locationData = $keyPairData['location'] ?? [];
                \assert(\is_array($locationData), 'location data must be an array');
                $region      = $locationData['regionName'] ?? '';
                $keyPairName = $keyPairData['name']        ?? '';

                $existingId = null;
                if (\is_string($keyPairName) && \is_string($region) && '' !== $keyPairName && '' !== $region) {
                    $existing   = $this->keyPairRepository->findOneByNameAndCredentialAndRegion($keyPairName, $credential, $region);
                    $existingId = $existing?->getId();
                }

                // 不立即刷新，等批量处理完成后统一刷新
                $keyPair = $this->updateKeyPairFromData($credential, $keyPairData, false);

                if (null !== $existingId) {
                    ++$stats['updated'];
                } else {
                    ++$stats['new'];
                }
                ++$stats['total'];
            } catch (\Throwable $e) {
                ++$stats['errors'];
                $this->logger->error('同步密钥对时出错', [
                    'keyPairData' => $keyPairData,
                    'credential'  => $credential->getName(),
                    'error'       => $e->getMessage(),
                ]);
            }
        }

        // 批量处理完成后统一刷新到数据库
        try {
            $this->entityManager->flush();
        } catch (\Throwable $e) {
            $this->logger->error('批量刷新密钥对数据到数据库时出错', [
                'credential' => $credential->getName(),
                'error'      => $e->getMessage(),
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
     * @param AwsCredential       $credential         AWS 凭证
     * @param array<int, string>  $remoteKeyPairNames 远程存在的密钥对名称列表
     * @param string              $region             区域
     *
     * @return int 删除的密钥对数量
     */
    public function cleanupDeletedKeyPairs(AwsCredential $credential, array $remoteKeyPairNames, string $region): int
    {
        // 获取本地所有密钥对
        $localKeyPairs = $this->keyPairRepository->findBy([
            'credential' => $credential,
            'region'     => $region,
        ]);

        $deletedCount = 0;
        foreach ($localKeyPairs as $localKeyPair) {
            if (!\in_array($localKeyPair->getName(), $remoteKeyPairNames, true)) {
                $this->logger->info('删除远程已不存在的密钥对', [
                    'name'       => $localKeyPair->getName(),
                    'credential' => $credential->getName(),
                    'region'     => $region,
                ]);
                $this->entityManager->remove($localKeyPair);
                ++$deletedCount;
            }
        }

        if ($deletedCount > 0) {
            $this->entityManager->flush();
        }

        return $deletedCount;
    }
}
