<?php

namespace AwsLightsailBundle\Command;

use Aws\Lightsail\LightsailClient;
use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Enum\AmazonRegion;
use AwsLightsailBundle\Enum\InstanceBlueprintEnum;
use AwsLightsailBundle\Enum\InstanceBundleEnum;
use AwsLightsailBundle\Repository\AwsCredentialRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'aws:lightsail:instance:create',
    description: '创建 AWS Lightsail 实例',
)]
class InstanceCreateCommand extends Command
{
    public function __construct(
        private readonly AwsCredentialRepository $credentialRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, '实例名称')
            ->addOption('credential-id', 'c', InputOption::VALUE_OPTIONAL, 'AWS 凭证 ID')
            ->addOption('region', 'r', InputOption::VALUE_OPTIONAL, '区域')
            ->addOption('blueprint', 'b', InputOption::VALUE_OPTIONAL, '蓝图 ID')
            ->addOption('bundle', null, InputOption::VALUE_OPTIONAL, '套餐 ID')
            ->addOption('availability-zone', 'z', InputOption::VALUE_OPTIONAL, '可用区')
            ->addOption('key-pair-name', 'k', InputOption::VALUE_OPTIONAL, '密钥对名称')
            ->addOption('tags', 't', InputOption::VALUE_OPTIONAL, '标签 (格式: key1=value1,key2=value2)')
            ->addOption('user-data', 'u', InputOption::VALUE_OPTIONAL, '用户数据 (脚本)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('创建 AWS Lightsail 实例');

        $instanceName = $input->getArgument('name');
        $helper = $this->getHelper('question');

        // 获取/选择凭证
        $credential = $this->getCredential($input, $output, $io, $helper);
        if (!$credential) {
            return Command::FAILURE;
        }

        // 获取/选择区域
        $region = $this->getRegion($input, $output, $credential, $helper);

        // 获取/选择蓝图
        $blueprint = $this->getBlueprint($input, $output, $helper);

        // 获取/选择套餐
        $bundle = $this->getBundle($input, $output, $helper);

        // 获取其他选项
        $availabilityZone = $input->getOption('availability-zone');
        $keyPairName = $input->getOption('key-pair-name');
        $userData = $input->getOption('user-data');

        // 解析标签
        $tags = [];
        if ($tagsOption = $input->getOption('tags')) {
            $tagPairs = explode(',', $tagsOption);
            foreach ($tagPairs as $pair) {
                [$key, $value] = explode('=', $pair, 2);
                $tags[] = [
                    'key' => trim($key),
                    'value' => trim($value),
                ];
            }
        }

        // 显示确认信息
        $io->section('将使用以下配置创建实例:');
        $io->table(
            ['参数', '值'],
            [
                ['实例名称', $instanceName],
                ['凭证', $credential->getName()],
                ['区域', $region],
                ['蓝图', $blueprint],
                ['套餐', $bundle],
                ['可用区', $availabilityZone ?: '默认'],
                ['密钥对', $keyPairName ?: '无'],
                ['标签', $tags ? json_encode($tags) : '无'],
                ['用户数据', $userData ? '已设置' : '无'],
            ]
        );

        if (!$io->confirm('确认创建?', true)) {
            $io->warning('已取消创建');
            return Command::SUCCESS;
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
            // 准备创建参数
            $params = [
                'instanceName' => $instanceName,
                'availabilityZone' => $availabilityZone,
                'blueprintId' => $blueprint,
                'bundleId' => $bundle,
                'tags' => $tags,
            ];

            if ($keyPairName) {
                $params['keyPairName'] = $keyPairName;
            }

            if ($userData) {
                $params['userData'] = $userData;
            }

            // 调用 API 创建实例
            $result = $client->createInstances($params);

            $io->success('实例创建请求已发送，请稍后使用 sync 命令同步实例状态');
            $io->note('操作 ID: ' . ($result->get('operations')[0]['id'] ?? '未知'));

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('创建实例时出错: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function getCredential(InputInterface $input, OutputInterface $output, SymfonyStyle $io, $helper): ?AwsCredential
    {
        $credentialId = $input->getOption('credential-id');
        if ($credentialId) {
            $credential = $this->credentialRepository->find($credentialId);
            if (!$credential) {
                $io->error('未找到指定的 AWS 凭证 (ID: ' . $credentialId . ')');
                return null;
            }
            return $credential;
        }

        // 获取所有凭证
        $credentials = $this->credentialRepository->findAll();
        if (empty($credentials)) {
            $io->error('未找到任何 AWS 凭证，请先添加凭证');
            return null;
        }

        // 如果只有一个凭证，直接使用
        if (count($credentials) === 1) {
            return $credentials[0];
        }

        // 如果有默认凭证，使用默认凭证
        $defaultCredential = $this->credentialRepository->findDefault();
        if ($defaultCredential) {
            $io->note('使用默认凭证: ' . $defaultCredential->getName());
            return $defaultCredential;
        }

        // 让用户选择凭证
        $credentialChoices = [];
        foreach ($credentials as $cred) {
            $credentialChoices[$cred->getId()] = $cred->getName();
        }

        $question = new ChoiceQuestion(
            '请选择AWS凭证:',
            $credentialChoices
        );
        $credentialId = $helper->ask($input, $output, $question);
        return $this->credentialRepository->find($credentialId);
    }

    private function getRegion(InputInterface $input, OutputInterface $output, AwsCredential $credential, $helper): string
    {
        $region = $input->getOption('region');
        if ($region) {
            return $region;
        }

        // 使用 AmazonRegion 枚举构建区域选择
        $regionChoices = [];
        foreach (AmazonRegion::cases() as $regionCase) {
            if ($regionCase !== AmazonRegion::NONE) {
                $regionChoices[$regionCase->value] = sprintf('%s (%s)', $regionCase->value, $regionCase->getLabel());
            }
        }

        $question = new ChoiceQuestion(
            '请选择区域:',
            $regionChoices,
            'us-east-1' // 默认使用 us-east-1
        );
        return $helper->ask($input, $output, $question);
    }

    private function getBlueprint(InputInterface $input, OutputInterface $output, $helper): string
    {
        $blueprint = $input->getOption('blueprint');
        if ($blueprint) {
            return $blueprint;
        }

        // 创建蓝图选项
        $blueprintChoices = [];
        foreach (InstanceBlueprintEnum::cases() as $case) {
            $blueprintChoices[$case->value] = $case->getLabel();
        }

        $question = new ChoiceQuestion(
            '请选择蓝图:',
            $blueprintChoices,
            'ubuntu_20_04' // 默认选择 Ubuntu 20.04
        );
        return $helper->ask($input, $output, $question);
    }

    private function getBundle(InputInterface $input, OutputInterface $output, $helper): string
    {
        $bundle = $input->getOption('bundle');
        if ($bundle) {
            return $bundle;
        }

        // 创建套餐选项
        $bundleChoices = [];
        foreach (InstanceBundleEnum::cases() as $case) {
            $bundleChoices[$case->value] = $case->getLabel();
        }

        $question = new ChoiceQuestion(
            '请选择套餐:',
            $bundleChoices,
            'micro_2_0' // 默认选择 Micro 套餐
        );
        return $helper->ask($input, $output, $question);
    }
}
