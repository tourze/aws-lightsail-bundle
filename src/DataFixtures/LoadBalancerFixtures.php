<?php

declare(strict_types=1);

namespace AwsLightsailBundle\DataFixtures;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\LoadBalancer;
use AwsLightsailBundle\Enum\LoadBalancerStatusEnum;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LoadBalancerFixtures extends AppFixtures implements DependentFixtureInterface
{
    public const LOAD_BALANCER_REFERENCE_PREFIX = 'load_balancer_';
    public const LOAD_BALANCER_COUNT            = 4;

    public function load(ObjectManager $manager): void
    {
        $credential = $this->getReference(AwsCredentialFixtures::CREDENTIAL_REFERENCE_PREFIX . 'default', AwsCredential::class);

        for ($i = 0; $i < self::LOAD_BALANCER_COUNT; ++$i) {
            $loadBalancer = $this->createLoadBalancer($credential);
            $manager->persist($loadBalancer);
            $this->addReference(self::LOAD_BALANCER_REFERENCE_PREFIX . $i, $loadBalancer);
        }

        $manager->flush();
    }

    private function createLoadBalancer(AwsCredential $credential): LoadBalancer
    {
        $loadBalancer = new LoadBalancer();
        $resourceName = $this->generateResourceName();
        $region       = $this->generateAwsRegion();
        $status       = $this->getRandomStatus();

        $this->configureBasicProperties($loadBalancer, $resourceName, $region, $status);
        $this->configureHealthCheck($loadBalancer);
        $loadBalancer->setConfigurationOptions($this->faker->boolean(60));
        $loadBalancer->setInstanceHealthSummary($this->generateInstanceHealthSummary());
        $loadBalancer->setTlsCertificateName($this->generateTlsCertificateName());
        $loadBalancer->setTags($this->generateLoadBalancerTags());
        $loadBalancer->setSyncTime($this->generateSyncTime());
        $loadBalancer->setCredential($credential);

        return $loadBalancer;
    }

    private function getRandomStatus(): LoadBalancerStatusEnum
    {
        $status = $this->faker->randomElement(LoadBalancerStatusEnum::cases());

        return $status instanceof LoadBalancerStatusEnum ? $status : LoadBalancerStatusEnum::ACTIVE;
    }

    private function configureBasicProperties(LoadBalancer $loadBalancer, string $resourceName, string $region, LoadBalancerStatusEnum $status): void
    {
        $loadBalancer->setName(\sprintf('%s-lb', $resourceName));
        $loadBalancer->setArn($this->generateAwsArn('lightsail', $region, 'load-balancer', $resourceName));
        $loadBalancer->setDnsName($this->generateDnsName($resourceName, $region));
        $loadBalancer->setRegion($region);
        $loadBalancer->setStatus($status);
    }

    private function generateDnsName(string $resourceName, string $region): string
    {
        $randomSuffix = $this->faker->randomLetter() . $this->faker->randomLetter();

        return \sprintf('%s-%s.%s.elb.amazonaws.com', $resourceName, $randomSuffix, $region);
    }

    private function configureHealthCheck(LoadBalancer $loadBalancer): void
    {
        $loadBalancer->setHealthCheckPort($this->getHealthCheckPort());
        $loadBalancer->setHealthCheckProtocol($this->getHealthCheckProtocol());
        $loadBalancer->setHealthCheckPath($this->getHealthCheckPath());
        $loadBalancer->setHealthCheckIntervalSeconds($this->getHealthCheckInterval());
        $loadBalancer->setHealthCheckTimeoutSeconds($this->getHealthCheckTimeout());
        $loadBalancer->setHealthyThreshold($this->faker->numberBetween(2, 10));
        $loadBalancer->setUnhealthyThreshold($this->faker->numberBetween(2, 10));
    }

    private function getHealthCheckPort(): int
    {
        $port = $this->faker->randomElement([80, 443, 8080]);

        return \is_int($port) ? $port : 80;
    }

    private function getHealthCheckProtocol(): string
    {
        $protocol = $this->faker->randomElement(['HTTP', 'HTTPS']);

        return \is_string($protocol) ? $protocol : 'HTTP';
    }

    private function getHealthCheckPath(): string
    {
        $path = $this->faker->randomElement(['/', '/health', '/status', '/ping']);

        return \is_string($path) ? $path : '/';
    }

    private function getHealthCheckInterval(): int
    {
        $interval = $this->faker->randomElement([30, 60, 300]);

        return \is_int($interval) ? $interval : 30;
    }

    private function getHealthCheckTimeout(): int
    {
        $timeout = $this->faker->randomElement([5, 10, 30]);

        return \is_int($timeout) ? $timeout : 5;
    }

    /**
     * @return array<string, int>
     */
    private function generateInstanceHealthSummary(): array
    {
        return [
            'total'     => $this->faker->numberBetween(1, 10),
            'healthy'   => $this->faker->numberBetween(0, 8),
            'unhealthy' => $this->faker->numberBetween(0, 3),
            'unknown'   => $this->faker->numberBetween(0, 2),
        ];
    }

    private function generateTlsCertificateName(): ?string
    {
        if (!$this->faker->boolean(60)) {
            return null;
        }

        return \sprintf('ELBSecurityPolicy-TLS-%d-%02d', $this->faker->numberBetween(2016, 2023), $this->faker->numberBetween(1, 12));
    }

    /**
     * @return array<string, string>
     */
    private function generateLoadBalancerTags(): array
    {
        $environment = $this->faker->randomElement(['dev', 'test', 'prod']);
        $protocol    = $this->faker->randomElement(['HTTP', 'HTTPS', 'TCP']);
        $purpose     = $this->faker->randomElement(['web', 'api', 'internal']);

        return [
            'Environment' => \is_string($environment) ? $environment : 'dev',
            'Protocol'    => \is_string($protocol) ? $protocol : 'HTTP',
            'Purpose'     => \is_string($purpose) ? $purpose : 'web',
        ];
    }

    public function getDependencies(): array
    {
        return [
            AwsCredentialFixtures::class,
        ];
    }
}
