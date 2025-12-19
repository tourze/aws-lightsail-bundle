<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Command;

use Aws\Lightsail\LightsailClient;
use Aws\Result;
use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Instance;
use AwsLightsailBundle\Enum\AmazonRegion;
use AwsLightsailBundle\Exception\InvalidOperationException;
use AwsLightsailBundle\Repository\AwsCredentialRepository;
use AwsLightsailBundle\Repository\InstanceRepository;
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
    description: '控制 AWS Lightsail 实例（启动/停止/重启）',
)]
final class InstanceControlCommand extends Command
{
    public const NAME = 'aws:lightsail:instance:control';

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
            ->addOption('force', 'f', InputOption::VALUE_NONE, '强制执行，不提示确认')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $operation = $this->validateOperation($input, $io);
        if (null === $operation) {
            return Command::FAILURE;
        }

        $operationName = $this->getOperationName($operation);
        $io->title($operationName . ' AWS Lightsail 实例');

        $instanceData = $this->getInstanceData($input, $output, $io);
        if (null === $instanceData) {
            return Command::FAILURE;
        }

        if (!$this->confirmOperation($input, $io, $operationName, $instanceData['instanceName'])) {
            $io->warning('操作已取消');

            return Command::SUCCESS;
        }

        return $this->executeOperation($io, $operation, $operationName, $instanceData);
    }

    private function validateOperation(InputInterface $input, SymfonyStyle $io): ?string
    {
        $operationArg = $input->getArgument('operation');
        if (!\is_string($operationArg)) {
            $io->error('操作类型参数必须是字符串');

            return null;
        }

        $operation = \strtolower($operationArg);
        if (!\in_array($operation, ['start', 'stop', 'reboot'], true)) {
            $io->error('无效的操作类型。可用操作: start, stop, reboot');

            return null;
        }

        return $operation;
    }

    private function getOperationName(string $operation): string
    {
        return match ($operation) {
            'start'  => '启动',
            'stop'   => '停止',
            'reboot' => '重启',
            default  => throw new InvalidOperationException("Unknown operation: {$operation}"),
        };
    }

    /**
     * @return array{instanceName: string, credential: AwsCredential, region: string}|null
     */
    private function getInstanceData(InputInterface $input, OutputInterface $output, SymfonyStyle $io): ?array
    {
        $instanceNameArg = $input->getArgument('instance-name');
        $instanceName    = \is_string($instanceNameArg) ? $instanceNameArg : null;
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        if (null === $instanceName || '' === $instanceName) {
            return $this->getInstanceFromSelection($input, $output, $io, $helper);
        }

        return $this->getInstanceFromName($input, $io, $instanceName);
    }

    /**
     * @return array{instanceName: string, credential: AwsCredential, region: string}|null
     */
    private function getInstanceFromSelection(InputInterface $input, OutputInterface $output, SymfonyStyle $io, QuestionHelper $helper): ?array
    {
        $instance = $this->selectInstance($input, $output, $io, $helper);
        if (null === $instance) {
            return null;
        }

        return [
            'instanceName' => $instance->getName(),
            'credential'   => $instance->getCredential(),
            'region'       => $instance->getRegion(),
        ];
    }

    /**
     * @return array{instanceName: string, credential: AwsCredential, region: string}|null
     */
    private function getInstanceFromName(InputInterface $input, SymfonyStyle $io, string $instanceName): ?array
    {
        $credentialIdOption = $input->getOption('credential-id');
        $credentialId       = \is_string($credentialIdOption) ? $credentialIdOption : null;
        $result             = $this->resolveCredential($credentialId, $instanceName, $io);
        if (null === $result) {
            return null;
        }

        $credential   = $result['credential'];
        $instance     = $result['instance'];
        $regionOption = $input->getOption('region');
        $region       = \is_string($regionOption) ? $regionOption : ($instance?->getRegion() ?? AmazonRegion::US_EAST_1->value);

        return [
            'instanceName' => $instanceName,
            'credential'   => $credential,
            'region'       => $region,
        ];
    }

    /**
     * @return array{credential: AwsCredential, instance: Instance|null}|null
     */
    private function resolveCredential(?string $credentialId, string $instanceName, SymfonyStyle $io): ?array
    {
        if (null !== $credentialId && '' !== $credentialId) {
            $credential = $this->credentialRepository->find($credentialId);
            if (null === $credential) {
                $io->error('未找到指定的 AWS 凭证');

                return null;
            }
            $instance = $this->instanceRepository->findOneBy(['name' => $instanceName, 'credential' => $credential]);

            return ['credential' => $credential, 'instance' => $instance];
        }

        $instance = $this->instanceRepository->findOneBy(['name' => $instanceName]);
        if (null !== $instance) {
            return ['credential' => $instance->getCredential(), 'instance' => $instance];
        }

        $credential = $this->getDefaultCredential($io);

        return null !== $credential ? ['credential' => $credential, 'instance' => null] : null;
    }

    private function getDefaultCredential(SymfonyStyle $io): ?AwsCredential
    {
        $credentials = $this->credentialRepository->findAll();
        if ([] === $credentials) {
            $io->error('未找到任何 AWS 凭证，请先添加凭证');

            return null;
        }

        foreach ($credentials as $cred) {
            if (true === $cred->isDefault()) {
                return $cred;
            }
        }

        $credential = $credentials[0];
        $io->note('使用凭证: ' . $credential->getName());

        return $credential;
    }

    private function confirmOperation(InputInterface $input, SymfonyStyle $io, string $operationName, string $instanceName): bool
    {
        $force = $input->getOption('force');
        if (true === $force) {
            return true;
        }

        return $io->confirm("确认要{$operationName}实例 {$instanceName}?", false);
    }

    /**
     * @param array{instanceName: string, credential: AwsCredential, region: string} $instanceData
     */
    private function executeOperation(SymfonyStyle $io, string $operation, string $operationName, array $instanceData): int
    {
        $client = new LightsailClient([
            'version'     => 'latest',
            'region'      => $instanceData['region'],
            'credentials' => [
                'key'    => $instanceData['credential']->getAccessKeyId(),
                'secret' => $instanceData['credential']->getSecretAccessKey(),
            ],
        ]);

        try {
            $result = $this->performOperation($client, $operation, $instanceData['instanceName']);

            $io->success("已发送{$operationName}命令到实例 {$instanceData['instanceName']}");
            $operations  = $result->get('operations');
            $operationId = '未知';
            if (\is_array($operations) && isset($operations[0]) && \is_array($operations[0]) && isset($operations[0]['id'])) {
                $id = $operations[0]['id'];
                if (\is_string($id) || \is_int($id)) {
                    $operationId = (string) $id;
                }
            }
            $io->note('操作 ID: ' . $operationId);

            $io->text('请使用以下命令同步实例状态:');
            $io->text('php bin/console aws:lightsail:instance:sync');

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $io->error("{$operationName}实例时出错: " . $e->getMessage());

            return Command::FAILURE;
        }
    }

    /**
     * @return Result<mixed>
     */
    private function performOperation(LightsailClient $client, string $operation, string $instanceName): Result
    {
        return match ($operation) {
            'start'  => $client->startInstance(['instanceName' => $instanceName]),
            'stop'   => $client->stopInstance(['instanceName' => $instanceName]),
            'reboot' => $client->rebootInstance(['instanceName' => $instanceName]),
            default  => throw new InvalidOperationException("Unknown operation: {$operation}"),
        };
    }

    private function selectInstance(InputInterface $input, OutputInterface $output, SymfonyStyle $io, QuestionHelper $helper): ?Instance
    {
        $credentialIdOption = $input->getOption('credential-id');
        $credentialId       = \is_string($credentialIdOption) ? $credentialIdOption : null;
        if (null !== $credentialId && '' !== $credentialId) {
            $credential = $this->credentialRepository->find($credentialId);
            if (null === $credential) {
                $io->error('未找到指定的 AWS 凭证');

                return null;
            }

            $instances = $this->instanceRepository->findBy(['credential' => $credential]);
        } else {
            $instances = $this->instanceRepository->findAll();
        }

        if ([] === $instances) {
            $io->error('未找到任何实例，请先同步实例列表');

            return null;
        }

        // 创建实例选项
        $instanceChoices = [];
        foreach ($instances as $instance) {
            $instanceChoices[$instance->getId()] = \sprintf(
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

        if (!\is_string($instanceId) && !\is_int($instanceId)) {
            $io->error('选择的实例ID无效');

            return null;
        }

        /** @var string|int $instanceId */

        return $this->instanceRepository->find($instanceId);
    }
}
