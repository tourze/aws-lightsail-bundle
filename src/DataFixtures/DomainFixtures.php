<?php

declare(strict_types=1);

namespace AwsLightsailBundle\DataFixtures;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Domain;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class DomainFixtures extends AppFixtures implements DependentFixtureInterface
{
    public const DOMAIN_REFERENCE_PREFIX = 'domain_';
    public const DOMAIN_COUNT            = 6;

    public function load(ObjectManager $manager): void
    {
        $credential = $this->getReference(AwsCredentialFixtures::CREDENTIAL_REFERENCE_PREFIX . 'default', AwsCredential::class);

        for ($i = 0; $i < self::DOMAIN_COUNT; ++$i) {
            $domain = new Domain();

            $domainName = $this->faker->domainName();
            $region     = $this->generateAwsRegion();

            $domain->setName($domainName);
            $domain->setArn($this->generateAwsArn('lightsail', $region, 'domain', \str_replace('.', '-', $domainName)));
            $domain->setRegion($region);
            $domain->setIsManaged($this->faker->boolean(80));
            $domain->setTags([
                'Environment' => $this->faker->randomElement(['dev', 'test', 'prod']),
                'Type'        => $this->faker->randomElement(['primary', 'subdomain', 'alias']),
                'Purpose'     => $this->faker->randomElement(['website', 'api', 'cdn']),
            ]);
            $domain->setSyncTime($this->generateSyncTime());
            $domain->setCredential($credential);

            $manager->persist($domain);
            $this->addReference(self::DOMAIN_REFERENCE_PREFIX . $i, $domain);
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
