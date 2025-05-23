<?php

namespace AwsLightsailBundle\Command;

use Aws\Lightsail\LightsailClient;
use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Instance;
use AwsLightsailBundle\Enum\InstanceBlueprintEnum;
use AwsLightsailBundle\Enum\InstanceBundleEnum;
use AwsLightsailBundle\Enum\InstanceStateEnum;
use AwsLightsailBundle\Repository\AwsCredentialRepository;
use AwsLightsailBundle\Repository\InstanceRepository;
use Doctrine\ORM\EntityManagerInterface;
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
        private readonly EntityManagerInterface $entityManager,
        private readonly InstanceRepository $instanceRepository,
        private readonly AwsCredentialRepository $credentialRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('credential-id', 'c', InputOption::VALUE_OPTIONAL, 'AWS 凭证 ID，不提供则使用所有凭证')
            ->addOption('region', 'r', InputOption::VALUE_OPTIONAL, '指定区域，不提供则使用凭证的默认区域');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('同步 AWS Lightsail 实例列表');

        $credentialId = $input->getOption('credential-id');
        $region = $input->getOption('region');

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

        $totalInstances = 0;
        $newInstances = 0;
        $updatedInstances = 0;

        foreach ($credentials as $credential) {
            $io->section('使用凭证: ' . $credential->getName());

            // 创建 Lightsail 客户端
            $client = new LightsailClient([
                'version' => 'latest',
                'region' => $region ?? $credential->getRegion(),
                'credentials' => [
                    'key' => $credential->getAccessKeyId(),
                    'secret' => $credential->getSecretAccessKey(),
                ],
            ]);

            try {
                // 调用 API 获取实例列表
                $result = $client->getInstances();
                $instances = $result->get('instances') ?? [];

                $io->progressStart(count($instances));

                foreach ($instances as $instanceData) {
                    // 更新数据库中的实例记录
                    $instance = $this->updateInstanceFromData($credential, $instanceData);
                    if ($instance->getId()) {
                        $updatedInstances++;
                    } else {
                        $newInstances++;
                    }
                    $totalInstances++;
                    $io->progressAdvance();
                }

                $io->progressFinish();
            } catch (\Exception $e) {
                $io->error('获取实例列表时出错: ' . $e->getMessage());
            }
        }

        $io->success(sprintf('同步完成。共同步 %d 个实例，其中新增 %d 个，更新 %d 个', $totalInstances, $newInstances, $updatedInstances));

        return Command::SUCCESS;
    }

    private function updateInstanceFromData(AwsCredential $credential, array $data): Instance
    {
        // 查找是否已存在此实例
        $instance = $this->instanceRepository->findOneByNameAndCredential($data['name'], $credential);

        // 如果不存在则创建新实例
        if (!$instance) {
            $instance = new Instance();
            $instance->setName($data['name']);
            $instance->setCredential($credential);
        }

        // 更新实例信息
        $instance->setArn($data['arn']);

        // 设置实例状态
        $stateValue = $data['state']['name'] ?? 'unknown';
        $instance->setState(InstanceStateEnum::fromString($stateValue));

        // 设置蓝图类型
        $blueprintId = $data['blueprintId'] ?? 'unknown';
        $instance->setBlueprint(InstanceBlueprintEnum::fromString($blueprintId));

        // 设置套餐
        $bundleId = $data['bundleId'] ?? 'unknown';
        $instance->setBundle(InstanceBundleEnum::fromString($bundleId));

        // 设置区域
        $instance->setRegion($data['location']['regionName'] ?? '');

        // 设置 IP 地址
        $instance->setPublicIpAddress($data['publicIpAddress'] ?? null);
        $instance->setPrivateIpAddress($data['privateIpAddress'] ?? null);

        // 设置密钥对
        $instance->setKeyPairName($data['sshKeyName'] ?? null);

        // 设置用户名
        $instance->setUsername($data['username'] ?? null);

        // 设置是否监控
        $instance->setIsMonitoring(isset($data['isMonitored']) ? (bool) $data['isMonitored'] : false);

        // 设置支持代码
        $instance->setSupportCode($data['supportCode'] ?? null);

        // 设置硬件信息
        if (isset($data['hardware'])) {
            $instance->setHardware($data['hardware']);
        }

        // 设置网络信息
        if (isset($data['networking'])) {
            $instance->setNetworking($data['networking']);
        }
        
        // 设置标签
        if (isset($data['tags'])) {
            $tags = [];
            foreach ($data['tags'] as $tag) {
                $tags[$tag['key']] = $tag['value'];
            }
            $instance->setTags($tags);
        }
        
        // 设置同步时间
        $instance->setSyncedAt(new \DateTimeImmutable());
        
        // 保存实例
        $this->entityManager->persist($instance);
        $this->entityManager->flush();
        
        return $instance;
    }
}
