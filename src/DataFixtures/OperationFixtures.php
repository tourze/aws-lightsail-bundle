<?php

declare(strict_types=1);

namespace AwsLightsailBundle\DataFixtures;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Operation;
use AwsLightsailBundle\Enum\OperationStatusEnum;
use AwsLightsailBundle\Enum\OperationTypeEnum;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class OperationFixtures extends AppFixtures implements DependentFixtureInterface
{
    public const OPERATION_REFERENCE_PREFIX = 'operation_';
    public const OPERATION_COUNT            = 12;

    public function load(ObjectManager $manager): void
    {
        $credential = $this->getReference(AwsCredentialFixtures::CREDENTIAL_REFERENCE_PREFIX . 'default', AwsCredential::class);

        for ($i = 0; $i < self::OPERATION_COUNT; ++$i) {
            $operation = $this->createOperation($credential);
            $manager->persist($operation);
            $this->addReference(self::OPERATION_REFERENCE_PREFIX . $i, $operation);
        }

        $manager->flush();
    }

    private function createOperation(AwsCredential $credential): Operation
    {
        $operation     = new Operation();
        $resourceName  = $this->generateResourceName();
        $region        = $this->generateAwsRegion();
        $operationType = $this->getRandomOperationType();
        $status        = $this->getRandomStatus();

        $this->configureBasicOperationProperties($operation, $resourceName, $region, $operationType, $status);
        $this->configureErrorDetails($operation, $status);
        $operation->setCompletionTime(\DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-30 days', 'now')));
        $operation->setMetadata($this->generateOperationMetadata($resourceName, $region, $operationType, $status));
        $operation->setCredential($credential);

        return $operation;
    }

    private function getRandomOperationType(): OperationTypeEnum
    {
        $operationType = $this->faker->randomElement(OperationTypeEnum::cases());

        return $operationType instanceof OperationTypeEnum ? $operationType : OperationTypeEnum::CREATE_INSTANCE;
    }

    private function getRandomStatus(): OperationStatusEnum
    {
        $status = $this->faker->randomElement(OperationStatusEnum::cases());

        return $status instanceof OperationStatusEnum ? $status : OperationStatusEnum::STARTED;
    }

    private function configureBasicOperationProperties(Operation $operation, string $resourceName, string $region, OperationTypeEnum $operationType, OperationStatusEnum $status): void
    {
        $operation->setOperationId($this->faker->uuid());
        $operation->setResourceName($resourceName);
        $operation->setResourceType($this->getResourceType());
        $operation->setType($operationType);
        $operation->setStatus($status);
        $operation->setRegion($region);
    }

    private function getResourceType(): ?string
    {
        $resourceType = $this->faker->randomElement(['Instance', 'LoadBalancer', 'Database', 'ContainerService', 'Disk']);

        return \is_string($resourceType) ? $resourceType : null;
    }

    private function configureErrorDetails(Operation $operation, OperationStatusEnum $status): void
    {
        if (OperationStatusEnum::FAILED !== $status) {
            $operation->setErrorCode(null);
            $operation->setErrorDetails(null);

            return;
        }

        $operation->setErrorCode($this->getErrorCode());
        $operation->setErrorDetails($this->faker->sentence());
    }

    private function getErrorCode(): ?string
    {
        $errorCode = $this->faker->randomElement(['InvalidParameterValue', 'ResourceNotFound', 'InsufficientCapacity']);

        return \is_string($errorCode) ? $errorCode : null;
    }

    /**
     * @return array<string, mixed>
     */
    private function generateOperationMetadata(string $resourceName, string $region, OperationTypeEnum $operationType, OperationStatusEnum $status): array
    {
        return [
            'location'         => $this->generateLocationMetadata($region),
            'isTerminal'       => $this->isTerminalStatus($status),
            'operationDetails' => $this->generateOperationDetails($resourceName, $operationType),
            'tags'             => $this->generateOperationTags($operationType, $status),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function generateLocationMetadata(string $region): array
    {
        $zoneChar = $this->faker->randomElement(['a', 'b', 'c']);

        return [
            'availabilityZone' => $region . (\is_string($zoneChar) ? $zoneChar : 'a'),
            'regionName'       => $region,
        ];
    }

    private function isTerminalStatus(OperationStatusEnum $status): bool
    {
        return \in_array($status, [OperationStatusEnum::SUCCEEDED, OperationStatusEnum::FAILED, OperationStatusEnum::COMPLETED], true);
    }

    /**
     * @return array<string, mixed>
     */
    private function generateOperationDetails(string $resourceName, OperationTypeEnum $operationType): array
    {
        return match ($operationType) {
            OperationTypeEnum::CREATE_INSTANCE => $this->generateCreateInstanceDetails($resourceName),
            OperationTypeEnum::DELETE_INSTANCE, OperationTypeEnum::START_INSTANCE, OperationTypeEnum::STOP_INSTANCE, OperationTypeEnum::REBOOT_INSTANCE => $this->generateInstanceOperationDetails($resourceName),
            OperationTypeEnum::CREATE_DISK => $this->generateCreateDiskDetails($resourceName),
            default                        => $this->generateDefaultOperationDetails($resourceName, $operationType),
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function generateCreateInstanceDetails(string $resourceName): array
    {
        return [
            'instanceName' => $resourceName,
            'bundleId'     => $this->faker->randomElement(['nano_2_0', 'micro_2_0', 'small_2_0']),
            'blueprintId'  => $this->faker->randomElement(['ubuntu_20_04', 'amazon_linux_2', 'wordpress']),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function generateInstanceOperationDetails(string $resourceName): array
    {
        return [
            'instanceName' => $resourceName,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function generateCreateDiskDetails(string $resourceName): array
    {
        return [
            'diskName' => $resourceName . '-disk',
            'sizeInGb' => $this->faker->randomElement([20, 40, 80]),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function generateDefaultOperationDetails(string $resourceName, OperationTypeEnum $operationType): array
    {
        return [
            'resourceName'  => $resourceName,
            'operationType' => $operationType->value,
        ];
    }

    /**
     * @return array<string, string>
     */
    private function generateOperationTags(OperationTypeEnum $operationType, OperationStatusEnum $status): array
    {
        $environment = $this->faker->randomElement(['dev', 'test', 'prod']);

        return [
            'OperationType' => $operationType->value,
            'Status'        => $status->value,
            'Environment'   => \is_string($environment) ? $environment : 'dev',
        ];
    }

    public function getDependencies(): array
    {
        return [
            AwsCredentialFixtures::class,
        ];
    }
}
