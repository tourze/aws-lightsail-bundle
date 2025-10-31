<?php

declare(strict_types=1);

namespace AwsLightsailBundle\DataFixtures;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Distribution;
use AwsLightsailBundle\Enum\DistributionStatusEnum;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class DistributionFixtures extends AppFixtures implements DependentFixtureInterface
{
    public const DISTRIBUTION_REFERENCE_PREFIX = 'distribution_';
    public const DISTRIBUTION_COUNT            = 5;

    public function load(ObjectManager $manager): void
    {
        $credential = $this->getReference(AwsCredentialFixtures::CREDENTIAL_REFERENCE_PREFIX . 'default', AwsCredential::class);

        for ($i = 0; $i < self::DISTRIBUTION_COUNT; ++$i) {
            $distribution = new Distribution();

            $resourceName = $this->generateResourceName();
            $region       = $this->generateAwsRegion();
            $status       = $this->faker->randomElement(DistributionStatusEnum::cases());

            $distribution->setName(\sprintf('%s-cdn', $resourceName));
            $distribution->setArn($this->generateAwsArn('lightsail', $region, 'distribution', $resourceName));
            $distribution->setDefaultDomainName(\sprintf('%s.cloudfront.net', $this->faker->md5()));
            $distribution->setStatus($status instanceof DistributionStatusEnum ? $status : DistributionStatusEnum::ACTIVE);
            $distribution->setRegion($region);
            $distribution->setOriginConfigs([
                'name'       => \sprintf('%s-origin', $resourceName),
                'domainName' => \sprintf('%s.%s.lightsail.aws', $resourceName, $region),
                'protocol'   => $this->faker->randomElement(['HTTP_ONLY', 'HTTPS_ONLY']),
            ]);
            $distribution->setDefaultCacheBehavior([
                'behavior'           => 'cache',
                'allowedHTTPMethods' => 'GET,HEAD',
                'cachedHTTPMethods'  => 'GET,HEAD',
                'cachePolicyName'    => 'default',
            ]);
            $distribution->setCacheBehaviors($this->faker->boolean(60) ? [
                'default' => [
                    'path'     => '/api/*',
                    'behavior' => 'dont-cache',
                ],
                'static' => [
                    'path'               => '/static/*',
                    'behavior'           => 'cache',
                    'allowedHTTPMethods' => 'GET,HEAD',
                ],
            ] : null);
            $distribution->setIsEnabled($this->faker->boolean(90));
            $distribution->setTags([
                'Environment' => $this->faker->randomElement(['dev', 'test', 'prod']),
                'Purpose'     => $this->faker->randomElement(['cdn', 'static', 'api']),
                'Status'      => $status instanceof DistributionStatusEnum ? $status->value : 'active',
            ]);
            $distribution->setSyncTime($this->generateSyncTime());
            $distribution->setCredential($credential);

            $manager->persist($distribution);
            $this->addReference(self::DISTRIBUTION_REFERENCE_PREFIX . $i, $distribution);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AwsCredentialFixtures::class,
        ];
    }
}
