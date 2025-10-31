<?php

declare(strict_types=1);

namespace AwsLightsailBundle\DataFixtures;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\StaticIp;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class StaticIpFixtures extends AppFixtures implements DependentFixtureInterface
{
    public const STATIC_IP_REFERENCE_PREFIX = 'static_ip_';
    public const STATIC_IP_COUNT            = 6;

    public function load(ObjectManager $manager): void
    {
        $credential = $this->getReference(AwsCredentialFixtures::CREDENTIAL_REFERENCE_PREFIX . 'default', AwsCredential::class);

        for ($i = 0; $i < self::STATIC_IP_COUNT; ++$i) {
            $staticIp = new StaticIp();

            $resourceName = $this->generateResourceName();
            $region       = $this->generateAwsRegion();
            $isAttached   = $this->faker->boolean(60);

            $staticIp->setName(\sprintf('%s-static-ip', $resourceName));
            $staticIp->setArn($this->generateAwsArn('lightsail', $region, 'static-ip', $resourceName));
            $staticIp->setIpAddress($this->faker->ipv4());
            $staticIp->setAttachedTo($isAttached ? \sprintf('%s-instance', $resourceName) : null);
            $staticIp->setIsAttached($isAttached);
            $staticIp->setRegion($region);
            $staticIp->setSyncTime($this->generateSyncTime());
            $staticIp->setCredential($credential);

            $manager->persist($staticIp);
            $this->addReference(self::STATIC_IP_REFERENCE_PREFIX . $i, $staticIp);
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
