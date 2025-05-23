<?php

namespace AwsLightsailBundle\Service;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Instance;
use AwsLightsailBundle\Enum\InstanceBlueprintEnum;
use AwsLightsailBundle\Enum\InstanceBundleEnum;
use AwsLightsailBundle\Enum\InstanceStateEnum;
use AwsLightsailBundle\Repository\InstanceRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * AWS Lightsail 实例同步服务
 */
class InstanceSyncService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly InstanceRepository $instanceRepository,
        private readonly KeyPairSyncService $keyPairSyncService,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * 从 AWS API 数据更新或创建实例
     *
     * @param AwsCredential $credential AWS 凭证
     * @param array $data AWS API 返回的实例数据
     * @param bool $flush 是否立即刷新到数据库，默认为 true
     * @return Instance 更新后的实例对象
     */
    public function updateInstanceFromData(AwsCredential $credential, array $data, bool $flush = true): Instance
    {
        $instanceName = $data['name'] ?? '';
        if (empty($instanceName)) {
            throw new \InvalidArgumentException('实例名称不能为空');
        }

        // 获取区域信息
        $region = $data['location']['regionName'] ?? '';
        if (empty($region)) {
            throw new \InvalidArgumentException('实例区域不能为空');
        }

        // 查找是否已存在此实例
        $instance = $this->instanceRepository->findOneByNameAndCredential($instanceName, $credential);

        // 如果不存在则创建新实例
        if (!$instance) {
            $instance = new Instance();
            $instance->setName($instanceName);
            $instance->setCredential($credential);
            $this->logger->info('创建新实例', ['name' => $instanceName, 'credential' => $credential->getName()]);
        } else {
            $this->logger->debug('更新现有实例', ['name' => $instanceName, 'credential' => $credential->getName()]);
        }

        // 更新基本信息
        $this->updateBasicFields($instance, $data, $credential, $region);

        // 更新状态信息
        $this->updateStateFields($instance, $data);

        // 更新蓝图和套餐信息
        $this->updateBlueprintAndBundleFields($instance, $data);

        // 更新位置信息
        $this->updateLocationFields($instance, $data);

        // 更新网络信息
        $this->updateNetworkFields($instance, $data);

        // 更新硬件和配置信息
        $this->updateHardwareAndConfigFields($instance, $data);

        // 更新时间戳
        $this->updateTimestampFields($instance, $data);

        // 设置同步时间
        $instance->setSyncedAt(Carbon::now());

        // 保存实例到持久化上下文
        $this->entityManager->persist($instance);

        // 根据参数决定是否立即刷新
        if ($flush) {
            $this->entityManager->flush();
        }

        return $instance;
    }

    /**
     * 更新基本字段
     */
    private function updateBasicFields(Instance $instance, array $data, AwsCredential $credential, string $region): void
    {
        // ARN
        if (isset($data['arn'])) {
            $instance->setArn($data['arn']);
        }

        // 支持代码
        if (isset($data['supportCode'])) {
            $instance->setSupportCode($data['supportCode']);
        }

        // 资源类型
        if (isset($data['resourceType'])) {
            $instance->setResourceType($data['resourceType']);
        }

        // 用户名
        if (isset($data['username'])) {
            $instance->setUsername($data['username']);
        }

        // SSH 密钥对关联
        if (isset($data['sshKeyName']) && !empty($data['sshKeyName'])) {
            $keyPair = $this->keyPairSyncService->findKeyPairByNameAndCredentialAndRegion(
                $data['sshKeyName'],
                $credential,
                $region
            );
            $instance->setKeyPair($keyPair);
        } else {
            $instance->setKeyPair(null);
        }
    }

    /**
     * 更新状态字段
     */
    private function updateStateFields(Instance $instance, array $data): void
    {
        // 实例状态
        if (isset($data['state']['name'])) {
            $stateValue = $data['state']['name'];
            try {
                $instance->setState(InstanceStateEnum::fromString($stateValue));
            } catch (\Exception $e) {
                $this->logger->warning('未知的实例状态', ['state' => $stateValue]);
                $instance->setState(InstanceStateEnum::UNKNOWN);
            }
        }

        // 状态代码
        if (isset($data['state']['code'])) {
            $instance->setStateCode((int)$data['state']['code']);
        }
    }

    /**
     * 更新蓝图和套餐字段
     */
    private function updateBlueprintAndBundleFields(Instance $instance, array $data): void
    {
        // 蓝图 ID
        if (isset($data['blueprintId'])) {
            try {
                $instance->setBlueprint(InstanceBlueprintEnum::fromString($data['blueprintId']));
            } catch (\Exception $e) {
                $this->logger->warning('未知的蓝图类型', ['blueprint' => $data['blueprintId']]);
                $instance->setBlueprint(InstanceBlueprintEnum::UBUNTU_20_04);
            }
        }

        // 蓝图名称
        if (isset($data['blueprintName'])) {
            $instance->setBlueprintName($data['blueprintName']);
        }

        // 套餐 ID
        if (isset($data['bundleId'])) {
            try {
                $instance->setBundle(InstanceBundleEnum::fromString($data['bundleId']));
            } catch (\Exception $e) {
                $this->logger->warning('未知的套餐类型', ['bundle' => $data['bundleId']]);
                $instance->setBundle(InstanceBundleEnum::MICRO_2_0);
            }
        }
    }

    /**
     * 更新位置字段
     */
    private function updateLocationFields(Instance $instance, array $data): void
    {
        // 区域
        if (isset($data['location']['regionName'])) {
            $instance->setRegion($data['location']['regionName']);
        }

        // 可用区
        if (isset($data['location']['availabilityZone'])) {
            $instance->setAvailabilityZone($data['location']['availabilityZone']);
        }
    }

    /**
     * 更新网络字段
     */
    private function updateNetworkFields(Instance $instance, array $data): void
    {
        // 公网 IP 地址
        if (isset($data['publicIpAddress'])) {
            $instance->setPublicIpAddress($data['publicIpAddress'] ?: null);
        }

        // 私网 IP 地址
        if (isset($data['privateIpAddress'])) {
            $instance->setPrivateIpAddress($data['privateIpAddress'] ?: null);
        }

        // IPv6 地址
        if (isset($data['ipv6Addresses']) && is_array($data['ipv6Addresses'])) {
            $instance->setIpv6Addresses($data['ipv6Addresses']);
        }

        // IP 地址类型
        if (isset($data['ipAddressType'])) {
            $instance->setIpAddressType($data['ipAddressType']);
        }

        // 是否为静态 IP
        if (isset($data['isStaticIp'])) {
            $instance->setIsStaticIp((bool)$data['isStaticIp']);
        }

        // 网络配置
        if (isset($data['networking'])) {
            $instance->setNetworking($data['networking']);
        }
    }

    /**
     * 更新硬件和配置字段
     */
    private function updateHardwareAndConfigFields(Instance $instance, array $data): void
    {
        // 硬件配置
        if (isset($data['hardware'])) {
            $instance->setHardware($data['hardware']);
        }

        // 元数据选项
        if (isset($data['metadataOptions'])) {
            $instance->setMetadataOptions($data['metadataOptions']);
        }

        // 标签
        if (isset($data['tags']) && is_array($data['tags'])) {
            $tags = [];
            foreach ($data['tags'] as $tag) {
                if (isset($tag['key']) && isset($tag['value'])) {
                    $tags[$tag['key']] = $tag['value'];
                }
            }
            $instance->setTags($tags);
        }

        // 监控状态
        if (isset($data['isMonitored'])) {
            $instance->setIsMonitoring((bool)$data['isMonitored']);
        }
    }

    /**
     * 更新时间戳字段
     */
    private function updateTimestampFields(Instance $instance, array $data): void
    {
        // AWS 创建时间
        if (isset($data['createdAt'])) {
            try {
                if ($data['createdAt'] instanceof \DateTime) {
                    $instance->setAwsCreatedAt(Carbon::parse($data['createdAt']));
                } elseif ($data['createdAt'] instanceof \DateTimeImmutable) {
                    $instance->setAwsCreatedAt($data['createdAt']);
                } elseif (is_string($data['createdAt'])) {
                    $instance->setAwsCreatedAt(Carbon::parse($data['createdAt']));
                } elseif (is_object($data['createdAt']) && method_exists($data['createdAt'], 'format')) {
                    // 处理 AWS SDK 的 DateTimeResult 对象
                    $dateString = $data['createdAt']->format('c');
                    $instance->setAwsCreatedAt(Carbon::parse($dateString));
                }
            } catch (\Exception $e) {
                $this->logger->warning('无法解析 AWS 创建时间', [
                    'createdAt' => $data['createdAt'],
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * 批量同步实例数据
     *
     * @param AwsCredential $credential AWS 凭证
     * @param array $instancesData AWS API 返回的实例数据数组
     * @return array 包含同步统计信息的数组
     */
    public function batchSyncInstances(AwsCredential $credential, array $instancesData): array
    {
        $stats = [
            'total' => 0,
            'new' => 0,
            'updated' => 0,
            'errors' => 0,
        ];

        foreach ($instancesData as $instanceData) {
            try {
                $existingId = null;
                if (isset($instanceData['name'])) {
                    $existing = $this->instanceRepository->findOneByNameAndCredential($instanceData['name'], $credential);
                    $existingId = $existing?->getId();
                }

                // 不立即刷新，等批量处理完成后统一刷新
                $instance = $this->updateInstanceFromData($credential, $instanceData, false);

                if ($existingId) {
                    $stats['updated']++;
                } else {
                    $stats['new']++;
                }
                $stats['total']++;
            } catch (\Exception $e) {
                $stats['errors']++;
                $this->logger->error('同步实例时出错', [
                    'instanceData' => $instanceData,
                    'credential' => $credential->getName(),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // 批量处理完成后统一刷新到数据库
        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $this->logger->error('批量刷新实例数据到数据库时出错', [
                'credential' => $credential->getName(),
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }

        return $stats;
    }

    /**
     * 清理远程已删除的实例
     *
     * @param AwsCredential $credential AWS 凭证
     * @param array $remoteInstanceNames 远程存在的实例名称列表
     * @return int 删除的实例数量
     */
    public function cleanupDeletedInstances(AwsCredential $credential, array $remoteInstanceNames): int
    {
        // 获取本地所有实例
        $localInstances = $this->instanceRepository->findBy([
            'credential' => $credential,
        ]);

        $deletedCount = 0;
        foreach ($localInstances as $localInstance) {
            if (!in_array($localInstance->getName(), $remoteInstanceNames, true)) {
                $this->logger->info('删除远程已不存在的实例', [
                    'name' => $localInstance->getName(),
                    'credential' => $credential->getName(),
                ]);
                $this->entityManager->remove($localInstance);
                $deletedCount++;
            }
        }

        if ($deletedCount > 0) {
            $this->entityManager->flush();
        }

        return $deletedCount;
    }
}
