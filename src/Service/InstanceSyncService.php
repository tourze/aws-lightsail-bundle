<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Service;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Instance;
use AwsLightsailBundle\Enum\InstanceBlueprintEnum;
use AwsLightsailBundle\Enum\InstanceBundleEnum;
use AwsLightsailBundle\Enum\InstanceStateEnum;
use AwsLightsailBundle\Exception\InvalidInstanceDataException;
use AwsLightsailBundle\Repository\InstanceRepository;
use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;

/**
 * AWS Lightsail 实例同步服务
 */
#[WithMonologChannel(channel: 'aws_lightsail')]
final readonly class InstanceSyncService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private InstanceRepository $instanceRepository,
        private KeyPairSyncService $keyPairSyncService,
        private LoggerInterface $logger,
        private InstanceDataUpdater $instanceDataUpdater,
    ) {
    }

    /**
     * 从 AWS API 数据更新或创建实例
     *
     * @param AwsCredential           $credential AWS 凭证
     * @param array<string, mixed>    $data       AWS API 返回的实例数据
     * @param bool                    $flush      是否立即刷新到数据库，默认为 true
     *
     * @return Instance 更新后的实例对象
     */
    public function updateInstanceFromData(AwsCredential $credential, array $data, bool $flush = true): Instance
    {
        $instanceName = $data['name'] ?? '';
        if (!\is_string($instanceName) || '' === $instanceName) {
            throw new InvalidInstanceDataException('实例名称不能为空');
        }

        // 获取区域信息
        $locationData = $data['location'] ?? [];
        \assert(\is_array($locationData), 'location data must be an array');
        $region = $locationData['regionName'] ?? '';
        if (!\is_string($region) || '' === $region) {
            throw new InvalidInstanceDataException('实例区域不能为空');
        }

        // 查找是否已存在此实例
        $instance = $this->instanceRepository->findOneByNameAndCredential($instanceName, $credential);

        // 如果不存在则创建新实例
        if (null === $instance) {
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
        $this->instanceDataUpdater->updateNetworkFields($instance, $data);

        // 更新硬件和配置信息
        $this->instanceDataUpdater->updateHardwareAndConfigFields($instance, $data);

        // 更新时间戳
        $this->instanceDataUpdater->updateTimestampFields($instance, $data);

        // 设置同步时间
        $instance->setSyncTime(CarbonImmutable::now());

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
     *
     * @param Instance                    $instance
     * @param array<string, mixed>        $data       AWS API 返回的实例数据
     * @param AwsCredential               $credential
     * @param string                      $region
     */
    private function updateBasicFields(Instance $instance, array $data, AwsCredential $credential, string $region): void
    {
        $this->updateBasicStringFields($instance, $data);
        $this->updateKeyPairAssociation($instance, $data, $credential, $region);
    }

    /**
     * 更新基本字符串字段
     *
     * @param Instance             $instance
     * @param array<string, mixed> $data
     */
    private function updateBasicStringFields(Instance $instance, array $data): void
    {
        // ARN
        if (isset($data['arn']) && \is_string($data['arn'])) {
            $instance->setArn($data['arn']);
        }

        // 支持代码
        if (isset($data['supportCode']) && \is_string($data['supportCode'])) {
            $instance->setSupportCode($data['supportCode']);
        }

        // 资源类型
        if (isset($data['resourceType']) && \is_string($data['resourceType'])) {
            $instance->setResourceType($data['resourceType']);
        }

        // 用户名
        if (isset($data['username']) && \is_string($data['username'])) {
            $instance->setUsername($data['username']);
        }
    }

    /**
     * 更新密钥对关联
     *
     * @param Instance             $instance
     * @param array<string, mixed> $data
     * @param AwsCredential        $credential
     * @param string               $region
     */
    private function updateKeyPairAssociation(Instance $instance, array $data, AwsCredential $credential, string $region): void
    {
        $sshKeyName = $data['sshKeyName'] ?? '';
        if (\is_string($sshKeyName) && '' !== $sshKeyName) {
            $keyPair = $this->keyPairSyncService->findKeyPairByNameAndCredentialAndRegion(
                $sshKeyName,
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
     *
     * @param Instance                    $instance
     * @param array<string, mixed>        $data     AWS API 返回的实例数据
     */
    private function updateStateFields(Instance $instance, array $data): void
    {
        $stateData = $data['state'] ?? [];
        \assert(\is_array($stateData), 'state data must be an array');

        // 实例状态
        if (isset($stateData['name']) && \is_string($stateData['name'])) {
            $stateValue = $stateData['name'];

            try {
                $instance->setState(InstanceStateEnum::fromString($stateValue));
            } catch (\Throwable $e) {
                $this->logger->warning('未知的实例状态', ['state' => $stateValue]);
                $instance->setState(InstanceStateEnum::UNKNOWN);
            }
        }

        // 状态代码
        if (isset($stateData['code']) && \is_int($stateData['code'])) {
            $instance->setStateCode($stateData['code']);
        }
    }

    /**
     * 更新蓝图和套餐字段
     *
     * @param Instance                    $instance
     * @param array<string, mixed>        $data     AWS API 返回的实例数据
     */
    private function updateBlueprintAndBundleFields(Instance $instance, array $data): void
    {
        // 蓝图 ID
        if (isset($data['blueprintId']) && \is_string($data['blueprintId'])) {
            $blueprintId = $data['blueprintId'];

            try {
                $instance->setBlueprint(InstanceBlueprintEnum::fromString($blueprintId));
            } catch (\Throwable $e) {
                $this->logger->warning('未知的蓝图类型', ['blueprint' => $blueprintId]);
                $instance->setBlueprint(InstanceBlueprintEnum::UBUNTU_20_04);
            }
        }

        // 蓝图名称
        if (isset($data['blueprintName']) && \is_string($data['blueprintName'])) {
            $instance->setBlueprintName($data['blueprintName']);
        }

        // 套餐 ID
        if (isset($data['bundleId']) && \is_string($data['bundleId'])) {
            $bundleId = $data['bundleId'];

            try {
                $instance->setBundle(InstanceBundleEnum::fromString($bundleId));
            } catch (\Throwable $e) {
                $this->logger->warning('未知的套餐类型', ['bundle' => $bundleId]);
                $instance->setBundle(InstanceBundleEnum::MICRO_2_0);
            }
        }
    }

    /**
     * 更新位置字段
     *
     * @param Instance                    $instance
     * @param array<string, mixed>        $data     AWS API 返回的实例数据
     */
    private function updateLocationFields(Instance $instance, array $data): void
    {
        $locationData = $data['location'] ?? [];
        \assert(\is_array($locationData), 'location data must be an array');

        // 区域
        if (isset($locationData['regionName']) && \is_string($locationData['regionName'])) {
            $instance->setRegion($locationData['regionName']);
        }

        // 可用区
        if (isset($locationData['availabilityZone']) && \is_string($locationData['availabilityZone'])) {
            $instance->setAvailabilityZone($locationData['availabilityZone']);
        }
    }

    /**
     * 批量同步实例数据
     *
     * @param AwsCredential                           $credential    AWS 凭证
     * @param array<int, array<string, mixed>>        $instancesData AWS API 返回的实例数据数组
     *
     * @return array{total: int, new: int, updated: int, errors: int} 包含同步统计信息的数组
     */
    public function batchSyncInstances(AwsCredential $credential, array $instancesData): array
    {
        $stats = [
            'total'   => 0,
            'new'     => 0,
            'updated' => 0,
            'errors'  => 0,
        ];

        foreach ($instancesData as $instanceData) {
            try {
                $existingId = null;
                if (isset($instanceData['name']) && \is_string($instanceData['name'])) {
                    $instanceName = $instanceData['name'];
                    $existing     = $this->instanceRepository->findOneByNameAndCredential($instanceName, $credential);
                    $existingId   = $existing?->getId();
                }

                // 不立即刷新,等批量处理完成后统一刷新
                $instance = $this->updateInstanceFromData($credential, $instanceData, false);

                if (null !== $existingId) {
                    ++$stats['updated'];
                } else {
                    ++$stats['new'];
                }
                ++$stats['total'];
            } catch (\Throwable $e) {
                ++$stats['errors'];
                $this->logger->error('同步实例时出错', [
                    'instanceData' => $instanceData,
                    'credential'   => $credential->getName(),
                    'error'        => $e->getMessage(),
                ]);
            }
        }

        // 批量处理完成后统一刷新到数据库
        try {
            $this->entityManager->flush();
        } catch (\Throwable $e) {
            $this->logger->error('批量刷新实例数据到数据库时出错', [
                'credential' => $credential->getName(),
                'error'      => $e->getMessage(),
            ]);

            throw $e;
        }

        return $stats;
    }

    /**
     * 清理远程已删除的实例
     *
     * @param AwsCredential       $credential          AWS 凭证
     * @param array<int, string>  $remoteInstanceNames 远程存在的实例名称列表
     *
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
            if (!\in_array($localInstance->getName(), $remoteInstanceNames, true)) {
                $this->logger->info('删除远程已不存在的实例', [
                    'name'       => $localInstance->getName(),
                    'credential' => $credential->getName(),
                ]);
                $this->entityManager->remove($localInstance);
                ++$deletedCount;
            }
        }

        if ($deletedCount > 0) {
            $this->entityManager->flush();
        }

        return $deletedCount;
    }
}
