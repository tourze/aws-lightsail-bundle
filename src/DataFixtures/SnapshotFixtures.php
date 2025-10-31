<?php

declare(strict_types=1);

namespace AwsLightsailBundle\DataFixtures;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Snapshot;
use AwsLightsailBundle\Enum\SnapshotTypeEnum;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SnapshotFixtures extends AppFixtures implements DependentFixtureInterface
{
    public const SNAPSHOT_REFERENCE_PREFIX = 'snapshot_';
    public const SNAPSHOT_COUNT            = 8;

    public function load(ObjectManager $manager): void
    {
        $credential = $this->getReference(AwsCredentialFixtures::CREDENTIAL_REFERENCE_PREFIX . 'default', AwsCredential::class);

        for ($i = 0; $i < self::SNAPSHOT_COUNT; ++$i) {
            $snapshot = new Snapshot();

            $resourceName = $this->generateResourceName();
            $region       = $this->generateAwsRegion();
            $snapshotType = $this->faker->randomElement(SnapshotTypeEnum::cases());

            $snapshot->setName(\sprintf('%s-snapshot-%s', $resourceName, $this->faker->date('Y-m-d')));
            $snapshot->setArn($this->generateAwsArn('lightsail', $region, 'instance-snapshot', $resourceName . '-snapshot'));
            $snapshot->setResourceName($resourceName);
            $snapshot->setType($snapshotType instanceof SnapshotTypeEnum ? $snapshotType : SnapshotTypeEnum::INSTANCE);
            $snapshot->setRegion($region);
            $environment = $this->faker->randomElement(['dev', 'test', 'prod']);
            $purpose     = $this->faker->randomElement(['backup', 'migration', 'template']);
            $createdBy   = $this->faker->randomElement(['manual', 'automated', 'script']);
            $snapshot->setTags([
                'Environment'  => \is_string($environment) ? $environment : 'dev',
                'SnapshotType' => $snapshotType instanceof SnapshotTypeEnum ? $snapshotType->value : 'instance',
                'Purpose'      => \is_string($purpose) ? $purpose : 'backup',
                'CreatedBy'    => \is_string($createdBy) ? $createdBy : 'manual',
            ]);
            $snapshot->setSyncTime($this->generateSyncTime());
            $snapshot->setCredential($credential);

            $manager->persist($snapshot);
            $this->addReference(self::SNAPSHOT_REFERENCE_PREFIX . $i, $snapshot);
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
