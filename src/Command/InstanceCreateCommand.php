<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Command;

use Aws\Lightsail\LightsailClient;
use Aws\Result;
use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Enum\AmazonRegion;
use AwsLightsailBundle\Enum\InstanceBlueprintEnum;
use AwsLightsailBundle\Enum\InstanceBundleEnum;
use AwsLightsailBundle\Repository\AwsCredentialRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: self::NAME,
    description: '创建 AWS Lightsail 实例',
)]
class InstanceCreateCommand extends Command
{
    public const NAME = 'aws:lightsail:instance:create';

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
            ->addOption('user-data', 'u', InputOption::VALUE_OPTIONAL, '用户数据 (脚本)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('创建 AWS Lightsail 实例');

        // 收集所有配置
        $config = $this->collectConfiguration($input, $output, $io);
        if (null === $config) {
            return Command::FAILURE;
        }

        // 显示确认信息
        if (!$this->confirmConfiguration($io, $config)) {
            $io->warning('已取消创建');

            return Command::SUCCESS;
        }

        // 创建实例
        return $this->createInstance($io, $config);
    }

    /**
     * @return array{instanceName: string, credential: AwsCredential, region: string, blueprint: string, bundle: string, availabilityZone: ?string, keyPairName: ?string, userData: ?string, tags: array<int, array{key: string, value: string}>}|null
     */
    private function collectConfiguration(InputInterface $input, OutputInterface $output, SymfonyStyle $io): ?array
    {
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        // 获取凭证
        $credential = $this->getCredential($input, $output, $io, $helper);
        if (null === $credential) {
            return null;
        }

        $nameArg = $input->getArgument('name');
        if (!\is_string($nameArg)) {
            $io->error('实例名称参数必须是字符串');

            return null;
        }

        $availabilityZoneOption = $input->getOption('availability-zone');
        $keyPairNameOption      = $input->getOption('key-pair-name');
        $userDataOption         = $input->getOption('user-data');
        $tagsOption             = $input->getOption('tags');

        return [
            'instanceName'     => $nameArg,
            'credential'       => $credential,
            'region'           => $this->getRegion($input, $output, $credential, $helper),
            'blueprint'        => $this->getBlueprint($input, $output, $helper),
            'bundle'           => $this->getBundle($input, $output, $helper),
            'availabilityZone' => \is_string($availabilityZoneOption) ? $availabilityZoneOption : null,
            'keyPairName'      => \is_string($keyPairNameOption) ? $keyPairNameOption : null,
            'userData'         => \is_string($userDataOption) ? $userDataOption : null,
            'tags'             => $this->parseTags(\is_string($tagsOption) ? $tagsOption : null),
        ];
    }

    /**
     * @return array<int, array{key: string, value: string}>
     */
    private function parseTags(?string $tagsOption): array
    {
        if (null === $tagsOption || '' === $tagsOption) {
            return [];
        }

        $tags     = [];
        $tagPairs = \explode(',', $tagsOption);
        foreach ($tagPairs as $pair) {
            [$key, $value] = \explode('=', $pair, 2);
            $tags[]        = [
                'key'   => \trim($key),
                'value' => \trim($value),
            ];
        }

        return $tags;
    }

    /**
     * @param array{instanceName: string, credential: AwsCredential, region: string, blueprint: string, bundle: string, availabilityZone: ?string, keyPairName: ?string, userData: ?string, tags: array<int, array{key: string, value: string}>} $config
     */
    private function confirmConfiguration(SymfonyStyle $io, array $config): bool
    {
        $io->section('将使用以下配置创建实例:');
        $io->table(
            ['参数', '值'],
            [
                ['实例名称', $config['instanceName']],
                ['凭证', $config['credential']->getName()],
                ['区域', $config['region']],
                ['蓝图', $config['blueprint']],
                ['套餐', $config['bundle']],
                ['可用区', $config['availabilityZone'] ?? '默认'],
                ['密钥对', $config['keyPairName'] ?? '无'],
                ['标签', [] !== $config['tags'] ? \json_encode($config['tags']) : '无'],
                ['用户数据', $this->formatUserDataDisplay($config['userData'])],
            ]
        );

        return $io->confirm('确认创建?', true);
    }

    private function formatUserDataDisplay(?string $userData): string
    {
        return (null !== $userData && '' !== $userData) ? '已设置' : '无';
    }

    /**
     * @param array{instanceName: string, credential: AwsCredential, region: string, blueprint: string, bundle: string, availabilityZone: ?string, keyPairName: ?string, userData: ?string, tags: array<int, array{key: string, value: string}>} $config
     */
    private function createInstance(SymfonyStyle $io, array $config): int
    {
        $client = $this->createLightsailClient($config['credential'], $config['region']);

        try {
            $params = $this->buildCreateInstanceParams($config);
            $result = $client->createInstances($params);

            $this->displaySuccessMessage($io, $result);

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $io->error('创建实例时出错: ' . $e->getMessage());

            return Command::FAILURE;
        }
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
     * @param array{instanceName: string, credential: AwsCredential, region: string, blueprint: string, bundle: string, availabilityZone: ?string, keyPairName: ?string, userData: ?string, tags: array<int, array{key: string, value: string}>} $config
     * @return array<string, mixed>
     */
    private function buildCreateInstanceParams(array $config): array
    {
        $params = [
            'instanceName'     => $config['instanceName'],
            'availabilityZone' => $config['availabilityZone'],
            'blueprintId'      => $config['blueprint'],
            'bundleId'         => $config['bundle'],
            'tags'             => $config['tags'],
        ];

        if (null !== $config['keyPairName'] && '' !== $config['keyPairName']) {
            $params['keyPairName'] = $config['keyPairName'];
        }

        if (null !== $config['userData'] && '' !== $config['userData']) {
            $params['userData'] = $config['userData'];
        }

        return $params;
    }

    /**
     * @param Result<mixed> $result
     */
    private function displaySuccessMessage(SymfonyStyle $io, Result $result): void
    {
        $io->success('实例创建请求已发送，请稍后使用 sync 命令同步实例状态');
        $operations  = $result->get('operations');
        $operationId = '未知';
        if (\is_array($operations) && isset($operations[0]) && \is_array($operations[0]) && isset($operations[0]['id'])) {
            $id = $operations[0]['id'];
            if (\is_string($id) || \is_int($id)) {
                $operationId = (string) $id;
            }
        }
        $io->note('操作 ID: ' . $operationId);
    }

    private function getCredential(InputInterface $input, OutputInterface $output, SymfonyStyle $io, QuestionHelper $helper): ?AwsCredential
    {
        $credentialIdOption = $input->getOption('credential-id');
        $credentialId       = \is_string($credentialIdOption) ? $credentialIdOption : null;

        if (null !== $credentialId && '' !== $credentialId) {
            return $this->getCredentialById($credentialId, $io);
        }

        $credentials = $this->credentialRepository->findAll();
        if ([] === $credentials) {
            $io->error('未找到任何 AWS 凭证，请先添加凭证');

            return null;
        }

        return $this->selectCredentialFromList($credentials, $input, $output, $io, $helper);
    }

    private function getCredentialById(string $credentialId, SymfonyStyle $io): ?AwsCredential
    {
        $credential = $this->credentialRepository->find($credentialId);
        if (null === $credential) {
            $io->error('未找到指定的 AWS 凭证 (ID: ' . $credentialId . ')');

            return null;
        }

        return $credential;
    }

    /**
     * @param AwsCredential[] $credentials
     */
    private function selectCredentialFromList(array $credentials, InputInterface $input, OutputInterface $output, SymfonyStyle $io, QuestionHelper $helper): ?AwsCredential
    {
        if (1 === \count($credentials)) {
            return $credentials[0];
        }

        $defaultCredential = $this->credentialRepository->findDefault();
        if (null !== $defaultCredential) {
            $io->note('使用默认凭证: ' . $defaultCredential->getName());

            return $defaultCredential;
        }

        return $this->askUserToSelectCredential($credentials, $input, $output, $io, $helper);
    }

    /**
     * @param AwsCredential[] $credentials
     */
    private function askUserToSelectCredential(array $credentials, InputInterface $input, OutputInterface $output, SymfonyStyle $io, QuestionHelper $helper): ?AwsCredential
    {
        $credentialChoices = [];
        foreach ($credentials as $cred) {
            $credentialChoices[$cred->getId()] = $cred->getName();
        }

        $question     = new ChoiceQuestion('请选择AWS凭证:', $credentialChoices);
        $credentialId = $helper->ask($input, $output, $question);

        if (!\is_string($credentialId) && !\is_int($credentialId)) {
            $io->error('选择的凭证ID无效');

            return null;
        }

        /** @var string|int $credentialId */
        return $this->credentialRepository->find($credentialId);
    }

    private function getRegion(InputInterface $input, OutputInterface $output, AwsCredential $credential, QuestionHelper $helper): string
    {
        $regionOption = $input->getOption('region');
        if (\is_string($regionOption) && '' !== $regionOption) {
            return $regionOption;
        }

        // 使用 AmazonRegion 枚举构建区域选择
        $regionChoices = [];
        foreach (AmazonRegion::cases() as $regionCase) {
            if (AmazonRegion::NONE !== $regionCase) {
                $regionChoices[$regionCase->value] = \sprintf('%s (%s)', $regionCase->value, $regionCase->getLabel());
            }
        }

        $question = new ChoiceQuestion(
            '请选择区域:',
            $regionChoices,
            'us-east-1' // 默认使用 us-east-1
        );

        $result = $helper->ask($input, $output, $question);

        if (!\is_string($result)) {
            throw new \InvalidArgumentException('无效的区域选择');
        }

        return $result;
    }

    private function getBlueprint(InputInterface $input, OutputInterface $output, QuestionHelper $helper): string
    {
        $blueprintOption = $input->getOption('blueprint');
        if (\is_string($blueprintOption) && '' !== $blueprintOption) {
            return $blueprintOption;
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

        $result = $helper->ask($input, $output, $question);

        if (!\is_string($result)) {
            throw new \InvalidArgumentException('无效的蓝图选择');
        }

        return $result;
    }

    private function getBundle(InputInterface $input, OutputInterface $output, QuestionHelper $helper): string
    {
        $bundleOption = $input->getOption('bundle');
        if (\is_string($bundleOption) && '' !== $bundleOption) {
            return $bundleOption;
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

        $result = $helper->ask($input, $output, $question);

        if (!\is_string($result)) {
            throw new \InvalidArgumentException('无效的套餐选择');
        }

        return $result;
    }
}
