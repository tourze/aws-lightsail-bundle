<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Command;

use Aws\Lightsail\LightsailClient;
use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Enum\AmazonRegion;
use AwsLightsailBundle\Repository\AwsCredentialRepository;
use AwsLightsailBundle\Service\InstanceSyncService;
use AwsLightsailBundle\Service\KeyPairSyncService;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: self::NAME,
    description: '同步 AWS Lightsail 实例列表',
)]
#[WithMonologChannel(channel: 'aws_lightsail')]
class InstanceSyncCommand extends Command
{
    public const NAME = 'aws:lightsail:instance:sync';

    public function __construct(
        private readonly AwsCredentialRepository $credentialRepository,
        private readonly InstanceSyncService $instanceSyncService,
        private readonly KeyPairSyncService $keyPairSyncService,
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('credential-id', 'c', InputOption::VALUE_OPTIONAL, 'AWS 凭证 ID，不提供则使用所有凭证')
            ->addOption('region', 'r', InputOption::VALUE_OPTIONAL, '指定区域，不提供则遍历所有区域')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('同步 AWS Lightsail 实例列表');

        return $this->performSync($input, $io);
    }

    private function performSync(InputInterface $input, SymfonyStyle $io): int
    {
        // 获取同步配置
        $syncConfig = $this->prepareSyncConfiguration($input, $io);
        if (null === $syncConfig) {
            return Command::FAILURE;
        }

        // 初始化统计并执行同步
        $stats = $this->executeSyncProcess($io, $syncConfig);

        // 显示结果
        $this->displaySyncResults($io, $stats);

        return Command::SUCCESS;
    }

    /**
     * @param array{credentials: AwsCredential[], regions: string[], totalCredentials: int, totalRegions: int} $syncConfig
     * @return array{totalInstances: int, newInstances: int, updatedInstances: int, errorInstances: int}
     */
    private function executeSyncProcess(SymfonyStyle $io, array $syncConfig): array
    {
        $stats = $this->initializeStatistics();

        $io->note(\sprintf(
            '将使用 %d 个凭证同步 %d 个区域',
            \count($syncConfig['credentials']),
            \count($syncConfig['regions'])
        ));

        foreach ($syncConfig['credentials'] as $credentialIndex => $credential) {
            $stats = $this->syncCredential(
                $io,
                $credential,
                $credentialIndex,
                $syncConfig,
                $stats
            );
        }

        return $stats;
    }

    /**
     * @return array{credentials: AwsCredential[], regions: string[], totalCredentials: int, totalRegions: int}|null
     */
    private function prepareSyncConfiguration(InputInterface $input, SymfonyStyle $io): ?array
    {
        $credentials = $this->getCredentials($input, $io);
        if (null === $credentials) {
            return null;
        }

        $regions = $this->getRegions($input);

        return [
            'credentials'      => $credentials,
            'regions'          => $regions,
            'totalCredentials' => \count($credentials),
            'totalRegions'     => \count($regions),
        ];
    }

    /**
     * @return AwsCredential[]|null
     */
    private function getCredentials(InputInterface $input, SymfonyStyle $io): ?array
    {
        $credentialId = $input->getOption('credential-id');

        if (null !== $credentialId && '' !== $credentialId) {
            $credential = $this->credentialRepository->find($credentialId);
            if (null === $credential) {
                $io->error('未找到指定的 AWS 凭证');

                return null;
            }

            return [$credential];
        }

        $credentials = $this->credentialRepository->findAll();
        if ([] === $credentials) {
            $io->error('未找到任何 AWS 凭证，请先添加凭证');

            return null;
        }

        return $credentials;
    }

    /**
     * @return array<string>
     */
    private function getRegions(InputInterface $input): array
    {
        $specifiedRegion = $input->getOption('region');

        if (null !== $specifiedRegion && '' !== $specifiedRegion) {
            if (!\is_string($specifiedRegion)) {
                throw new \InvalidArgumentException('Region option must be a string');
            }

            return [$specifiedRegion];
        }

        // 遍历所有区域（排除 NONE）
        $regions = [];
        foreach (AmazonRegion::cases() as $regionCase) {
            if (AmazonRegion::NONE !== $regionCase) {
                $regions[] = $regionCase->value;
            }
        }

        return $regions;
    }

    /**
     * @return array{totalInstances: int, newInstances: int, updatedInstances: int, errorInstances: int}
     */
    private function initializeStatistics(): array
    {
        return [
            'totalInstances'   => 0,
            'newInstances'     => 0,
            'updatedInstances' => 0,
            'errorInstances'   => 0,
        ];
    }

    /**
     * @param array{credentials: AwsCredential[], regions: string[], totalCredentials: int, totalRegions: int} $syncConfig
     * @param array{totalInstances: int, newInstances: int, updatedInstances: int, errorInstances: int} $stats
     * @return array{totalInstances: int, newInstances: int, updatedInstances: int, errorInstances: int}
     */
    private function syncCredential(
        SymfonyStyle $io,
        AwsCredential $credential,
        int $credentialIndex,
        array $syncConfig,
        array $stats,
    ): array {
        $io->section(\sprintf(
            '凭证 %d/%d: %s',
            $credentialIndex + 1,
            $syncConfig['totalCredentials'],
            $credential->getName()
        ));

        [$stats, $resourceNames] = $this->syncAllRegions($io, $credential, $syncConfig, $stats);
        $this->cleanupDeletedResources($io, $credential, $resourceNames['instances'], $resourceNames['keyPairs']);

        return $stats;
    }

    /**
     * @param array{credentials: AwsCredential[], regions: string[], totalCredentials: int, totalRegions: int} $syncConfig
     * @param array{totalInstances: int, newInstances: int, updatedInstances: int, errorInstances: int} $stats
     * @return array{0: array{totalInstances: int, newInstances: int, updatedInstances: int, errorInstances: int}, 1: array{instances: string[], keyPairs: array<string, string[]>}}
     */
    private function syncAllRegions(
        SymfonyStyle $io,
        AwsCredential $credential,
        array $syncConfig,
        array $stats,
    ): array {
        $allRemoteInstanceNames = [];
        $regionKeyPairs         = [];

        foreach ($syncConfig['regions'] as $regionIndex => $region) {
            [$stats, $regionData] = $this->syncRegion(
                $io,
                $credential,
                $region,
                $regionIndex,
                $syncConfig['totalRegions'],
                $stats
            );

            if (null !== $regionData) {
                $allRemoteInstanceNames = \array_merge(
                    $allRemoteInstanceNames,
                    $regionData['instanceNames']
                );
                if ([] !== ($regionData['keyPairNames'] ?? [])) {
                    $regionKeyPairs[$region] = $regionData['keyPairNames'];
                }
            }
        }

        return [$stats, ['instances' => $allRemoteInstanceNames, 'keyPairs' => $regionKeyPairs]];
    }

    /**
     * @param array{totalInstances: int, newInstances: int, updatedInstances: int, errorInstances: int} $stats
     * @return array{0: array{totalInstances: int, newInstances: int, updatedInstances: int, errorInstances: int}, 1: array{keyPairNames: string[], instanceNames: string[]}|null}
     */
    private function syncRegion(
        SymfonyStyle $io,
        AwsCredential $credential,
        string $region,
        int $regionIndex,
        int $totalRegions,
        array $stats,
    ): array {
        $this->displayRegionInfo($io, $region, $regionIndex, $totalRegions);
        $client = $this->createLightsailClient($credential, $region);

        try {
            return $this->performRegionSync($io, $client, $credential, $stats);
        } catch (\Throwable $e) {
            $this->handleRegionError($io, $e, $region, $credential);

            return [$stats, null];
        }
    }

    private function displayRegionInfo(SymfonyStyle $io, string $region, int $regionIndex, int $totalRegions): void
    {
        $io->text(\sprintf(
            '区域 %d/%d: %s (%s)',
            $regionIndex + 1,
            $totalRegions,
            $region,
            $this->getRegionLabel($region)
        ));
    }

    /**
     * @param array{totalInstances: int, newInstances: int, updatedInstances: int, errorInstances: int} $stats
     * @return array{0: array{totalInstances: int, newInstances: int, updatedInstances: int, errorInstances: int}, 1: array{keyPairNames: string[], instanceNames: string[]}}
     */
    private function performRegionSync(
        SymfonyStyle $io,
        LightsailClient $client,
        AwsCredential $credential,
        array $stats,
    ): array {
        $keyPairNames                   = $this->syncKeyPairs($io, $client, $credential);
        [$updatedStats, $instanceNames] = $this->syncInstances($io, $client, $credential, $stats);

        $regionData = [
            'keyPairNames'  => $keyPairNames,
            'instanceNames' => $instanceNames,
        ];

        return [$updatedStats, $regionData];
    }

    private function createLightsailClient(AwsCredential $credential, string $region): LightsailClient
    {
        return new LightsailClient([
            'version'     => 'latest',
            'region'      => $region,
            'credentials' => [
                'key'    => $credential->getAccessKeyId(),
                'secret' => $credential->getSecretAccessKey(),
            ],
        ]);
    }

    /**
     * @return array<string>
     */
    private function syncKeyPairs(
        SymfonyStyle $io,
        LightsailClient $client,
        AwsCredential $credential,
    ): array {
        $io->text('  → 同步密钥对...');
        $keyPairsResult = $client->getKeyPairs();
        $keyPairsData   = $keyPairsResult->get('keyPairs');

        if (!\is_array($keyPairsData)) {
            $this->logger->warning('AWS API returned non-array data for keyPairs', [
                'credential' => $credential->getName(),
                'data_type'  => \get_debug_type($keyPairsData),
            ]);

            return [];
        }

        $keyPairs           = $this->validateKeyPairsData($keyPairsData);
        $remoteKeyPairNames = $this->extractKeyPairNames($keyPairs);

        if ([] !== $keyPairs) {
            $keyPairStats = $this->keyPairSyncService->batchSyncKeyPairs($credential, $keyPairs);
            $io->text(\sprintf(
                '  → 同步了 %d 个密钥对 (新增 %d, 更新 %d)',
                $keyPairStats['total'],
                $keyPairStats['new'],
                $keyPairStats['updated']
            ));
        }

        return $remoteKeyPairNames;
    }

    /**
     * @param array{totalInstances: int, newInstances: int, updatedInstances: int, errorInstances: int} $stats
     * @return array{0: array{totalInstances: int, newInstances: int, updatedInstances: int, errorInstances: int}, 1: string[]}
     */
    private function syncInstances(
        SymfonyStyle $io,
        LightsailClient $client,
        AwsCredential $credential,
        array $stats,
    ): array {
        $result        = $client->getInstances();
        $instancesData = $result->get('instances');

        if (!\is_array($instancesData)) {
            $this->logger->warning('AWS API returned non-array data for instances', [
                'credential' => $credential->getName(),
                'data_type'  => \get_debug_type($instancesData),
            ]);

            return [$stats, []];
        }

        $instances = $this->validateInstancesData($instancesData);

        if ([] === $instances) {
            $io->text('  → 该区域没有实例');

            return [$stats, []];
        }

        $instanceCount = \count($instances);
        $io->text(\sprintf('  → 找到 %d 个实例', $instanceCount));
        $io->progressStart($instanceCount);

        // 使用 Service 批量同步实例
        $syncStats = $this->instanceSyncService->batchSyncInstances($credential, $instances);
        $stats     = $this->updateStatistics($stats, $syncStats);

        $io->progressFinish();

        if ($syncStats['errors'] > 0) {
            $io->text(\sprintf('  → 同步完成，其中 %d 个出错', $syncStats['errors']));
        }

        $instanceNames = $this->extractInstanceNames($instances);

        return [$stats, $instanceNames];
    }

    /**
     * @param array{totalInstances: int, newInstances: int, updatedInstances: int, errorInstances: int} $stats
     * @param array{total: int, new: int, updated: int, errors: int} $syncStats
     * @return array{totalInstances: int, newInstances: int, updatedInstances: int, errorInstances: int}
     */
    private function updateStatistics(array $stats, array $syncStats): array
    {
        $stats['totalInstances']   += $syncStats['total'];
        $stats['newInstances']     += $syncStats['new'];
        $stats['updatedInstances'] += $syncStats['updated'];
        $stats['errorInstances']   += $syncStats['errors'];

        return $stats;
    }

    private function handleRegionError(
        SymfonyStyle $io,
        \Throwable $e,
        string $region,
        AwsCredential $credential,
    ): void {
        $this->logger->error('获取实例列表时出错', [
            'region'     => $region,
            'credential' => $credential->getName(),
            'exception'  => $e,
        ]);
        $io->text(\sprintf('  → 获取该区域实例时出错: %s', $e->getMessage()));
    }

    /**
     * @param string[] $allRemoteInstanceNames
     * @param array<string, string[]> $regionKeyPairs
     */
    private function cleanupDeletedResources(
        SymfonyStyle $io,
        AwsCredential $credential,
        array $allRemoteInstanceNames,
        array $regionKeyPairs,
    ): void {
        $io->text('清理已删除的资源...');

        // 清理实例
        $deletedInstances = $this->instanceSyncService->cleanupDeletedInstances($credential, \array_values($allRemoteInstanceNames));
        if ($deletedInstances > 0) {
            $io->text(\sprintf('删除了 %d 个远程已不存在的实例', $deletedInstances));
        }

        // 清理各区域的密钥对
        foreach ($regionKeyPairs as $region => $remoteKeyPairNames) {
            $deletedKeyPairs = $this->keyPairSyncService->cleanupDeletedKeyPairs($credential, \array_values($remoteKeyPairNames), $region);
            if ($deletedKeyPairs > 0) {
                $io->text(\sprintf('删除了 %d 个 %s 区域远程已不存在的密钥对', $deletedKeyPairs, $region));
            }
        }
    }

    /**
     * @param array{totalInstances: int, newInstances: int, updatedInstances: int, errorInstances: int} $stats
     */
    private function displaySyncResults(SymfonyStyle $io, array $stats): void
    {
        $message = \sprintf(
            '同步完成。共同步 %d 个实例，其中新增 %d 个，更新 %d 个',
            $stats['totalInstances'],
            $stats['newInstances'],
            $stats['updatedInstances']
        );

        if ($stats['errorInstances'] > 0) {
            $message .= \sprintf('，%d 个出错', $stats['errorInstances']);
            $io->warning($message);
        } else {
            $io->success($message);
        }
    }

    private function getRegionLabel(string $region): string
    {
        // 尝试从 AmazonRegion 枚举获取标签
        foreach (AmazonRegion::cases() as $regionCase) {
            if ($regionCase->value === $region) {
                return $regionCase->getLabel();
            }
        }

        return $region;
    }

    /**
     * @param mixed $keyPairsData
     * @return array<int, array<string, mixed>>
     */
    private function validateKeyPairsData($keyPairsData): array
    {
        if (!\is_array($keyPairsData)) {
            return [];
        }

        return $this->validateArrayData($keyPairsData, 'key pair');
    }

    /**
     * @param array<int, array<string, mixed>> $keyPairs
     * @return array<string>
     */
    private function extractKeyPairNames(array $keyPairs): array
    {
        return \array_map(fn (array $keyPair): string => $this->extractName($keyPair, 'key_pair'), $keyPairs);
    }

    /**
     * @param mixed $instancesData
     * @return array<int, array<string, mixed>>
     */
    private function validateInstancesData($instancesData): array
    {
        if (!\is_array($instancesData)) {
            return [];
        }

        return $this->validateArrayData($instancesData, 'instance');
    }

    /**
     * @param mixed $data
     * @return array<int, array<string, mixed>>
     */
    private function validateArrayData($data, string $type): array
    {
        if (!\is_iterable($data)) {
            $this->logger->warning(\sprintf('Data is not iterable for %s validation', $type), [
                'data_type' => \get_debug_type($data),
            ]);

            return [];
        }

        $validated = [];
        $index     = 0;

        foreach ($data as $item) {
            if (!\is_array($item)) {
                $this->logInvalidDataStructure($index, $item, $type);
                ++$index;

                continue;
            }

            $validated[$index] = $this->extractStringKeyedData($item);
            ++$index;
        }

        return $validated;
    }

    private function logInvalidDataStructure(int $index, mixed $item, string $type): void
    {
        $this->logger->warning(\sprintf('Invalid %s data structure', $type), [
            'index'     => $index,
            'data_type' => \get_debug_type($item),
        ]);
    }

    /**
     * @param array<mixed> $item
     * @return array<string, mixed>
     */
    private function extractStringKeyedData(array $item): array
    {
        $typedData = [];
        foreach ($item as $key => $value) {
            if (\is_string($key)) {
                $typedData[$key] = $value;
            }
        }

        return $typedData;
    }

    /**
     * @param array<int, array<string, mixed>> $instances
     * @return array<string>
     */
    private function extractInstanceNames(array $instances): array
    {
        return \array_map(fn (array $instance): string => $this->extractName($instance, 'instance'), $instances);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function extractName(array $data, string $type): string
    {
        if (isset($data['name']) && \is_string($data['name'])) {
            return $data['name'];
        }

        $this->logger->warning(\sprintf('%s missing or invalid name field', \ucfirst($type)), [
            $type => $data,
        ]);

        return '';
    }
}
