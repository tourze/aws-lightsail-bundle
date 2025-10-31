<?php

declare(strict_types=1);

namespace AwsLightsailBundle\DataFixtures;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\ContainerService;
use AwsLightsailBundle\Enum\ContainerServicePowerEnum;
use AwsLightsailBundle\Enum\ContainerServiceStateEnum;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ContainerServiceFixtures extends AppFixtures implements DependentFixtureInterface
{
    public const CONTAINER_SERVICE_REFERENCE_PREFIX = 'container_service_';
    public const CONTAINER_SERVICE_COUNT            = 8;

    public function load(ObjectManager $manager): void
    {
        $credential = $this->getReference(AwsCredentialFixtures::CREDENTIAL_REFERENCE_PREFIX . 'default', AwsCredential::class);

        for ($i = 0; $i < self::CONTAINER_SERVICE_COUNT; ++$i) {
            $containerService = $this->createContainerService($credential);
            $manager->persist($containerService);
            $this->addReference(self::CONTAINER_SERVICE_REFERENCE_PREFIX . $i, $containerService);
        }

        $manager->flush();
    }

    private function createContainerService(AwsCredential $credential): ContainerService
    {
        $containerService = new ContainerService();
        $resourceName     = $this->generateResourceName();
        $region           = $this->generateAwsRegion();
        $state            = $this->getRandomState();

        $this->configureBasicProperties($containerService, $resourceName, $region, $state);
        $this->configureDeployments($containerService, $state);
        $this->configureDomains($containerService, $resourceName, $region);
        $containerService->setContainerImages($this->generateContainerImages());
        $containerService->setTags($this->generateContainerServiceTags());
        $containerService->setSyncTime($this->generateSyncTime());
        $containerService->setCredential($credential);

        return $containerService;
    }

    private function getRandomState(): ContainerServiceStateEnum
    {
        $state = $this->faker->randomElement(ContainerServiceStateEnum::cases());

        return $state instanceof ContainerServiceStateEnum ? $state : ContainerServiceStateEnum::PENDING;
    }

    private function configureBasicProperties(ContainerService $containerService, string $resourceName, string $region, ContainerServiceStateEnum $state): void
    {
        $power      = $this->faker->randomElement(ContainerServicePowerEnum::cases());
        $domainWord = $this->faker->domainWord();

        $containerService->setName(\sprintf('%s-container', $resourceName));
        $containerService->setArn($this->generateAwsArn('lightsail', $region, 'container-service', $resourceName));
        $containerService->setPower($power instanceof ContainerServicePowerEnum ? $power : ContainerServicePowerEnum::NANO);
        $containerService->setScale($this->faker->numberBetween(1, 10));
        $containerService->setState($state);
        $containerService->setRegion($region);
        $containerService->setUrl($this->generateContainerUrl($resourceName, $domainWord));
    }

    private function generateContainerUrl(string $resourceName, mixed $domainWord): ?string
    {
        if (!$this->faker->boolean(70)) {
            return null;
        }

        $domain = \is_string($domainWord) ? $domainWord : 'app';

        return \sprintf('https://%s.%s.us-east-1.lightsail.aws', $resourceName, $domain);
    }

    private function configureDeployments(ContainerService $containerService, ContainerServiceStateEnum $state): void
    {
        $containerService->setCurrentDeployment($this->generateCurrentDeployment($state));
        $containerService->setNextDeployment($this->generateNextDeployment());
    }

    /**
     * @return array<string, mixed>|null
     */
    private function generateCurrentDeployment(ContainerServiceStateEnum $state): ?array
    {
        if (ContainerServiceStateEnum::RUNNING !== $state) {
            return null;
        }

        return [
            'version'    => $this->faker->numberBetween(1, 100),
            'state'      => 'ACTIVE',
            'containers' => [
                'web' => [
                    'image' => 'nginx:latest',
                    'ports' => ['80' => 'HTTP'],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function generateNextDeployment(): ?array
    {
        if (!$this->faker->boolean(30)) {
            return null;
        }

        return [
            'version'    => $this->faker->numberBetween(101, 200),
            'state'      => 'ACTIVATING',
            'containers' => [
                'web' => [
                    'image' => 'nginx:1.21',
                    'ports' => ['80' => 'HTTP'],
                ],
            ],
        ];
    }

    private function configureDomains(ContainerService $containerService, string $resourceName, string $region): void
    {
        $containerService->setIsPublicDomainEnabled($this->faker->boolean(80));
        $containerService->setIsPrivateDomainEnabled($this->faker->boolean(30));
        $containerService->setPrivateDomainName($this->generatePrivateDomainName($resourceName));
        $containerService->setPublicDomainNames($this->generatePublicDomainNames($resourceName, $region));
    }

    /**
     * @return array<string, string>|null
     */
    private function generatePrivateDomainName(string $resourceName): ?array
    {
        if (!$this->faker->boolean(30)) {
            return null;
        }

        return [
            'certificateName' => \sprintf('%s-cert', $resourceName),
            'domainName'      => $this->faker->domainName(),
        ];
    }

    private function generatePublicDomainNames(string $resourceName, string $region): ?string
    {
        if (!$this->faker->boolean(80)) {
            return null;
        }

        return \sprintf('%s.%s.lightsail.aws', $resourceName, $region);
    }

    /**
     * @return array<string, string>
     */
    private function generateContainerImages(): array
    {
        $nginxVersion = $this->faker->randomElement(['latest', 'stable', '1.21', '1.20']);
        $phpVersion   = $this->faker->randomElement(['8.1', '8.2', '8.3']);

        return [
            'web' => \sprintf('public.ecr.aws/nginx/nginx:%s', \is_string($nginxVersion) ? $nginxVersion : 'latest'),
            'app' => \sprintf('public.ecr.aws/php/php:%s', \is_string($phpVersion) ? $phpVersion : '8.2'),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function generateContainerServiceTags(): array
    {
        $environment = $this->faker->randomElement(['dev', 'test', 'prod']);
        $project     = $this->faker->word();
        $owner       = $this->faker->userName();

        return [
            'Environment' => \is_string($environment) ? $environment : 'dev',
            'Project'     => \is_string($project) ? $project : 'default',
            'Owner'       => \is_string($owner) ? $owner : 'admin',
        ];
    }

    public function getDependencies(): array
    {
        return [
            AwsCredentialFixtures::class,
        ];
    }
}
