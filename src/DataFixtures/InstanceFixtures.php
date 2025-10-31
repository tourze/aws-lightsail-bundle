<?php

declare(strict_types=1);

namespace AwsLightsailBundle\DataFixtures;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Instance;
use AwsLightsailBundle\Enum\InstanceBlueprintEnum;
use AwsLightsailBundle\Enum\InstanceBundleEnum;
use AwsLightsailBundle\Enum\InstanceStateEnum;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class InstanceFixtures extends AppFixtures implements DependentFixtureInterface
{
    public const INSTANCE_REFERENCE_PREFIX = 'instance_';
    public const INSTANCE_COUNT            = 10;

    public function load(ObjectManager $manager): void
    {
        $credential = $this->getReference(AwsCredentialFixtures::CREDENTIAL_REFERENCE_PREFIX . 'default', AwsCredential::class);

        for ($i = 0; $i < self::INSTANCE_COUNT; ++$i) {
            $instance = $this->createInstance($credential);
            $manager->persist($instance);
            $this->addReference(self::INSTANCE_REFERENCE_PREFIX . $i, $instance);
        }

        $manager->flush();
    }

    private function createInstance(AwsCredential $credential): Instance
    {
        $instance     = new Instance();
        $resourceName = $this->generateResourceName();
        $region       = $this->generateAwsRegion();
        $state        = $this->getRandomState();
        $blueprint    = $this->getRandomBlueprint();
        $bundle       = $this->getRandomBundle();

        $this->configureBasicInstanceProperties($instance, $resourceName, $region, $state, $blueprint, $bundle);
        $this->configureNetworkSettings($instance, $state);
        $instance->setTags($this->generateInstanceTags($blueprint, $bundle));
        $instance->setHardware($this->generateHardwareConfig());
        $instance->setNetworking($this->generateNetworkingConfig());
        $instance->setMetadataOptions($this->generateMetadataOptions());
        $instance->setAwsCreationTime($this->generateCreationTime());
        $instance->setUsername($this->getUsernameForBlueprint($blueprint));
        $instance->setIsMonitoring($this->faker->boolean(70));
        $instance->setSupportCode($this->faker->uuid());
        $instance->setSyncTime($this->generateSyncTime());
        $instance->setCredential($credential);

        return $instance;
    }

    private function getRandomState(): InstanceStateEnum
    {
        $state = $this->faker->randomElement(InstanceStateEnum::cases());

        return $state instanceof InstanceStateEnum ? $state : InstanceStateEnum::PENDING;
    }

    private function getRandomBlueprint(): InstanceBlueprintEnum
    {
        $blueprint = $this->faker->randomElement(InstanceBlueprintEnum::cases());

        return $blueprint instanceof InstanceBlueprintEnum ? $blueprint : InstanceBlueprintEnum::UBUNTU_20_04;
    }

    private function getRandomBundle(): InstanceBundleEnum
    {
        $bundle = $this->faker->randomElement(InstanceBundleEnum::cases());

        return $bundle instanceof InstanceBundleEnum ? $bundle : InstanceBundleEnum::NANO_2_0;
    }

    private function configureBasicInstanceProperties(Instance $instance, string $resourceName, string $region, InstanceStateEnum $state, InstanceBlueprintEnum $blueprint, InstanceBundleEnum $bundle): void
    {
        $instance->setName(\sprintf('%s-instance', $resourceName));
        $instance->setArn($this->generateAwsArn('lightsail', $region, 'instance', $resourceName));
        $instance->setState($state);
        $instance->setStateCode($this->faker->numberBetween(0, 80));
        $instance->setBlueprint($blueprint);
        $instance->setBlueprintName($blueprint->value);
        $instance->setBundle($bundle);
        $instance->setRegion($region);
        $instance->setAvailabilityZone($this->generateAvailabilityZone($region));
        $instance->setResourceType('Instance');
    }

    private function generateAvailabilityZone(string $region): string
    {
        $zoneChar = $this->faker->randomElement(['a', 'b', 'c']);

        return $region . (\is_string($zoneChar) ? $zoneChar : 'a');
    }

    private function configureNetworkSettings(Instance $instance, InstanceStateEnum $state): void
    {
        $instance->setPublicIpAddress($this->getPublicIp($state));
        $instance->setPrivateIpAddress($this->getPrivateIp($state));
        $instance->setIpv6Addresses($this->getIpv6Addresses($state));
        $instance->setIpAddressType($this->getIpAddressType());
        $instance->setIsStaticIp($this->faker->boolean(30));
    }

    private function getPublicIp(InstanceStateEnum $state): ?string
    {
        if (InstanceStateEnum::RUNNING !== $state) {
            return null;
        }

        return $this->faker->ipv4();
    }

    private function getPrivateIp(InstanceStateEnum $state): ?string
    {
        if (InstanceStateEnum::RUNNING !== $state) {
            return null;
        }

        return $this->faker->localIpv4();
    }

    /**
     * @return array<int, string>|null
     */
    private function getIpv6Addresses(InstanceStateEnum $state): ?array
    {
        if (!$this->faker->boolean(30) || InstanceStateEnum::RUNNING !== $state) {
            return null;
        }

        return [$this->faker->ipv6()];
    }

    private function getIpAddressType(): ?string
    {
        $ipAddressType = $this->faker->randomElement(['ipv4', 'dualstack']);

        return \is_string($ipAddressType) ? $ipAddressType : null;
    }

    /**
     * @return array<string, string>
     */
    private function generateInstanceTags(InstanceBlueprintEnum $blueprint, InstanceBundleEnum $bundle): array
    {
        $environment = $this->faker->randomElement(['dev', 'test', 'prod']);
        $purpose     = $this->faker->randomElement(['web', 'api', 'database', 'cache']);

        return [
            'Environment' => \is_string($environment) ? $environment : 'dev',
            'Blueprint'   => $blueprint->value,
            'Bundle'      => $bundle->value,
            'Purpose'     => \is_string($purpose) ? $purpose : 'web',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function generateHardwareConfig(): array
    {
        return [
            'cpuCount'    => $this->faker->numberBetween(1, 8),
            'ramSizeInGb' => $this->faker->randomElement([0.5, 1, 2, 4, 8, 16]),
            'disks'       => [
                [
                    'name'         => 'root',
                    'sizeInGb'     => $this->faker->randomElement([20, 40, 80]),
                    'isSystemDisk' => true,
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function generateNetworkingConfig(): array
    {
        return [
            'monthlyTransfer' => [
                'gbPerMonthAllocated' => $this->faker->numberBetween(1000, 10000),
            ],
            'ports' => [
                [
                    'fromPort'   => 22,
                    'toPort'     => 22,
                    'protocol'   => 'tcp',
                    'accessType' => 'public',
                ],
                [
                    'fromPort'   => 80,
                    'toPort'     => 80,
                    'protocol'   => 'tcp',
                    'accessType' => 'public',
                ],
                [
                    'fromPort'   => 443,
                    'toPort'     => 443,
                    'protocol'   => 'tcp',
                    'accessType' => 'public',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function generateMetadataOptions(): array
    {
        return [
            'state'                   => $this->faker->randomElement(['optional', 'required']),
            'httpTokens'              => $this->faker->randomElement(['optional', 'required']),
            'httpPutResponseHopLimit' => $this->faker->numberBetween(1, 64),
        ];
    }

    private function generateCreationTime(): ?\DateTimeImmutable
    {
        if (!$this->faker->boolean(70)) {
            return null;
        }

        return \DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-1 year', 'now'));
    }

    private function getUsernameForBlueprint(InstanceBlueprintEnum $blueprint): string
    {
        return match ($blueprint) {
            InstanceBlueprintEnum::UBUNTU_20_04, InstanceBlueprintEnum::UBUNTU_22_04, InstanceBlueprintEnum::UBUNTU_18_04 => 'ubuntu',
            InstanceBlueprintEnum::AMAZON_LINUX_2, InstanceBlueprintEnum::AMAZON_LINUX_2023 => 'ec2-user',
            InstanceBlueprintEnum::DEBIAN_10, InstanceBlueprintEnum::DEBIAN_11, InstanceBlueprintEnum::DEBIAN_12 => 'admin',
            default => 'admin',
        };
    }

    public function getDependencies(): array
    {
        return [
            AwsCredentialFixtures::class,
        ];
    }
}
