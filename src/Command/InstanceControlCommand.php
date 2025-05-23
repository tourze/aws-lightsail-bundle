<?php

namespace AwsLightsailBundle\Command;

use Aws\Lightsail\LightsailClient;
use AwsLightsailBundle\Entity\Instance;
use AwsLightsailBundle\Repository\AwsCredentialRepository;
use AwsLightsailBundle\Repository\InstanceRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'aws:lightsail:instance:control',
    description: '控制 AWS Lightsail 实例（启动/停止/重启）',
)]
class InstanceControlCommand extends Command
{
    public function __construct(
        private readonly InstanceRepository $instanceRepository,
        private readonly AwsCredentialRepository $credentialRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('operation', InputArgument::REQUIRED, '操作类型（start/stop/reboot）')
            ->addArgument('instance-name', InputArgument::OPTIONAL, '实例名称')
            ->addOption('credential-id', 'c', InputOption::VALUE_OPTIONAL, 'AWS 凭证 ID')
            ->addOption('region', 'r', InputOption::VALUE_OPTIONAL, '区域，不提供则使用实例所在区域')
            ->addOption('force', 'f', InputOption::VALUE_NONE, '强制执行，不提示确认');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // 验证操作类型
        $operation = strtolower($input->getArgument('operation'));
        if (!in_array($operation, ['start', 'stop', 'reboot'])) {
            $io->error('无效的操作类型。可用操作: start, stop, reboot');
            return Command::FAILURE;
        }

        // 获取操作的中文名称
        $operationName = match ($operation) {
            'start' => '启动',
            'stop' => '停止',
            'reboot' => '重启',
        };

        $io->title($operationName . ' AWS Lightsail 实例');

        // 获取实例
        $instanceName = $input->getArgument('instance-name');
        $helper = $this->getHelper('question');

        if (!$instanceName) {
            $instance = $this->selectInstance($input, $output, $io, $helper);
            if (!$instance) {
                return Command::FAILURE;
            }
            $instanceName = $instance->getName();
            $credential = $instance->getCredential();
            $region = $instance->getRegion();
        } else {
            // 使用名称查找实例
            $credentialId = $input->getOption('credential-id');
            if ($credentialId) {
                $credential = $this->credentialRepository->find($credentialId);
                if (!$credential) {
                    $io->error('未找到指定的 AWS 凭证');
                    return Command::FAILURE;
                }

                $instance = $this->instanceRepository->findOneBy([
                    'name' => $instanceName,
                    'credential' => $credential,
                ]);
            } else {
                $instance = $this->instanceRepository->findOneBy([
                    'name' => $instanceName,
                ]);

                if ($instance) {
                    $credential = $instance->getCredential();
                } else {
                    // 尝试查找所有凭证
                    $credentials = $this->credentialRepository->findAll();
                    if (empty($credentials)) {
                        $io->error('未找到任何 AWS 凭证，请先添加凭证');
                        return Command::FAILURE;
                    }

                    // 使用默认凭证或第一个凭证
                    $credential = null;
                    foreach ($credentials as $cred) {
                        if ($cred->isDefault()) {
                            $credential = $cred;
                            break;
                        }
                    }

                    if (!$credential) {
                        $credential = $credentials[0];
                    }

                    $io->note('使用凭证: ' . $credential->getName());
                }
            }

            $region = $input->getOption('region') ?? ($instance ? $instance->getRegion() : $credential->getRegion());
        }

        // 确认操作
        $force = $input->getOption('force');
        if (!$force) {
            if (!$io->confirm("确认要{$operationName}实例 {$instanceName}?", false)) {
                $io->warning('操作已取消');
                return Command::SUCCESS;
            }
        }

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
            // 执行操作
            switch ($operation) {
                case 'start':
                    $result = $client->startInstance(['instanceName' => $instanceName]);
                    break;
                case 'stop':
                    $result = $client->stopInstance(['instanceName' => $instanceName]);
                    break;
                case 'reboot':
                    $result = $client->rebootInstance(['instanceName' => $instanceName]);
                    break;
            }

            $io->success("已发送{$operationName}命令到实例 {$instanceName}");
            $io->note('操作 ID: ' . ($result->get('operations')[0]['id'] ?? '未知'));

            // 提示用户同步状态
            $io->text('请使用以下命令同步实例状态:');
            $io->text('php bin/console aws:lightsail:instance:sync');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error("{$operationName}实例时出错: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function selectInstance(InputInterface $input, OutputInterface $output, SymfonyStyle $io, $helper): ?Instance
    {
        $credentialId = $input->getOption('credential-id');
        if ($credentialId) {
            $credential = $this->credentialRepository->find($credentialId);
            if (!$credential) {
                $io->error('未找到指定的 AWS 凭证');
                return null;
            }

            $instances = $this->instanceRepository->findByCredential($credential);
        } else {
            $instances = $this->instanceRepository->findAll();
        }

        if (empty($instances)) {
            $io->error('未找到任何实例，请先同步实例列表');
            return null;
        }

        // 创建实例选项
        $instanceChoices = [];
        foreach ($instances as $instance) {
            $instanceChoices[$instance->getId()] = sprintf(
                '%s (%s, %s, %s)',
                $instance->getName(),
                $instance->getState()->getLabel(),
                $instance->getRegion(),
                $instance->getCredential()->getName()
            );
        }

        $question = new ChoiceQuestion(
            '请选择实例:',
            $instanceChoices
        );

        $instanceId = $helper->ask($input, $output, $question);
        return $this->instanceRepository->find($instanceId);
    }
}
