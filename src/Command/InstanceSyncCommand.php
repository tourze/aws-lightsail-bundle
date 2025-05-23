<?php

namespace AwsLightsailBundle\Command;

use Aws\Lightsail\LightsailClient;
use AwsLightsailBundle\Enum\AmazonRegion;
use AwsLightsailBundle\Repository\AwsCredentialRepository;
use AwsLightsailBundle\Service\InstanceSyncService;
use AwsLightsailBundle\Service\KeyPairSyncService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'aws:lightsail:instance:sync',
    description: '同步 AWS Lightsail 实例列表',
)]
class InstanceSyncCommand extends Command
{
    public function __construct(
        private readonly AwsCredentialRepository $credentialRepository,
        private readonly InstanceSyncService $instanceSyncService,
        private readonly KeyPairSyncService $keyPairSyncService,
        private readonly LoggerInterface $logger,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('credential-id', 'c', InputOption::VALUE_OPTIONAL, 'AWS 凭证 ID，不提供则使用所有凭证')
            ->addOption('region', 'r', InputOption::VALUE_OPTIONAL, '指定区域，不提供则遍历所有区域');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('同步 AWS Lightsail 实例列表');

        $credentialId = $input->getOption('credential-id');
        $specifiedRegion = $input->getOption('region');

        // 获取 AWS 凭证
        if ($credentialId) {
            $credentials = [$this->credentialRepository->find($credentialId)];
            if (!$credentials[0]) {
                $io->error('未找到指定的 AWS 凭证');
                return Command::FAILURE;
            }
        } else {
            $credentials = $this->credentialRepository->findAll();
            if (empty($credentials)) {
                $io->error('未找到任何 AWS 凭证，请先添加凭证');
                return Command::FAILURE;
            }
        }

        // 确定要同步的区域列表
        $regions = [];
        if ($specifiedRegion) {
            $regions = [$specifiedRegion];
        } else {
            // 遍历所有区域（排除 NONE）
            foreach (AmazonRegion::cases() as $regionCase) {
                if ($regionCase !== AmazonRegion::NONE) {
                    $regions[] = $regionCase->value;
                }
            }
        }

        $totalInstances = 0;
        $newInstances = 0;
        $updatedInstances = 0;
        $errorInstances = 0;
        $totalRegions = count($regions);
        $totalCredentials = count($credentials);

        $io->note(sprintf('将使用 %d 个凭证同步 %d 个区域', $totalCredentials, $totalRegions));

        foreach ($credentials as $credentialIndex => $credential) {
            $io->section(sprintf('凭证 %d/%d: %s', $credentialIndex + 1, $totalCredentials, $credential->getName()));

            $allRemoteInstanceNames = [];
            $regionKeyPairs = [];

            foreach ($regions as $regionIndex => $region) {
                $io->text(sprintf('区域 %d/%d: %s (%s)', 
                    $regionIndex + 1, 
                    $totalRegions, 
                    $region,
                    $this->getRegionLabel($region)
                ));

                // 创建 Lightsail 客户端
                $client = new LightsailClient([
                    'version' => 'latest',
                    'region' => $region,
                    'credentials' => [
                        'key' => $credential->getAccessKeyId(),
                        'secret' => $credential->getSecretAccessKey(),
                    ],
                ]);

                try {
                    // 先同步密钥对
                    $io->text('  → 同步密钥对...');
                    $keyPairsResult = $client->getKeyPairs();
                    $keyPairs = $keyPairsResult->get('keyPairs') ?? [];

                    $remoteKeyPairNames = array_map(fn($kp) => $kp['name'] ?? '', $keyPairs);
                    $regionKeyPairs[$region] = $remoteKeyPairNames;

                    if (!empty($keyPairs)) {
                        $keyPairStats = $this->keyPairSyncService->batchSyncKeyPairs($credential, $keyPairs);
                        $io->text(sprintf('  → 同步了 %d 个密钥对 (新增 %d, 更新 %d)', 
                            $keyPairStats['total'], $keyPairStats['new'], $keyPairStats['updated']));
                    }

                    // 调用 API 获取实例列表
                    $result = $client->getInstances();
                    $instances = $result->get('instances') ?? [];

                    // 收集所有远程实例名称
                    $remoteInstanceNames = array_map(fn($instance) => $instance['name'] ?? '', $instances);
                    $allRemoteInstanceNames = array_merge($allRemoteInstanceNames, $remoteInstanceNames);

                    if (empty($instances)) {
                        $io->text('  → 该区域没有实例');
                        continue;
                    }

                    $io->text(sprintf('  → 找到 %d 个实例', count($instances)));
                    $io->progressStart(count($instances));

                    // 使用 Service 批量同步实例
                    $stats = $this->instanceSyncService->batchSyncInstances($credential, $instances);

                    $totalInstances += $stats['total'];
                    $newInstances += $stats['new'];
                    $updatedInstances += $stats['updated'];
                    $errorInstances += $stats['errors'];

                    $io->progressFinish();

                    if ($stats['errors'] > 0) {
                        $io->text(sprintf('  → 同步完成，其中 %d 个出错', $stats['errors']));
                    }
                } catch (\Exception $e) {
                    $this->logger->error('获取实例列表时出错', [
                        'region' => $region,
                        'credential' => $credential->getName(),
                        'exception' => $e,
                    ]);
                    $io->text(sprintf('  → 获取该区域实例时出错: %s', $e->getMessage()));
                }
            }

            // 清理该凭证下已删除的资源
            $io->text('清理已删除的资源...');

            // 清理实例
            $deletedInstances = $this->instanceSyncService->cleanupDeletedInstances($credential, $allRemoteInstanceNames);
            if ($deletedInstances > 0) {
                $io->text(sprintf('删除了 %d 个远程已不存在的实例', $deletedInstances));
            }

            // 清理各区域的密钥对
            foreach ($regionKeyPairs as $region => $remoteKeyPairNames) {
                $deletedKeyPairs = $this->keyPairSyncService->cleanupDeletedKeyPairs($credential, $remoteKeyPairNames, $region);
                if ($deletedKeyPairs > 0) {
                    $io->text(sprintf('删除了 %d 个 %s 区域远程已不存在的密钥对', $deletedKeyPairs, $region));
                }
            }
        }

        $message = sprintf('同步完成。共同步 %d 个实例，其中新增 %d 个，更新 %d 个', 
            $totalInstances, $newInstances, $updatedInstances);

        if ($errorInstances > 0) {
            $message .= sprintf('，%d 个出错', $errorInstances);
            $io->warning($message);
        } else {
            $io->success($message);
        }

        return Command::SUCCESS;
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
}
