<?php

declare(strict_types=1);

namespace AwsLightsailBundle\DataFixtures;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\DiskSnapshot;
use AwsLightsailBundle\Enum\SnapshotTypeEnum;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class DiskSnapshotFixtures extends AppFixtures implements DependentFixtureInterface
{
    public const DISK_SNAPSHOT_REFERENCE_PREFIX = 'disk_snapshot_';
    public const DISK_SNAPSHOT_COUNT            = 9;

    public function load(ObjectManager $manager): void
    {
        $credential = $this->getReference(AwsCredentialFixtures::CREDENTIAL_REFERENCE_PREFIX . 'default', AwsCredential::class);

        for ($i = 0; $i < self::DISK_SNAPSHOT_COUNT; ++$i) {
            $diskSnapshot = new DiskSnapshot();

            $resourceName = $this->generateResourceName();
            $region       = $this->generateAwsRegion();
            $snapshotType = $this->faker->randomElement(SnapshotTypeEnum::cases());

            $sizeInGb = $this->faker->randomElement([8, 20, 40, 80, 160, 320]);
            $sizeInGb = \is_int($sizeInGb) ? $sizeInGb : 20;

            $diskSnapshot->setName(\sprintf('%s-disk-snapshot-%s', $resourceName, $this->faker->date('Y-m-d')));
            $diskSnapshot->setArn($this->generateAwsArn('lightsail', $region, 'disk-snapshot', $resourceName . '-snapshot'));
            $diskSnapshot->setDiskName(\sprintf('%s-disk', $resourceName));
            $diskSnapshot->setSizeInGb($sizeInGb);
            $diskSnapshot->setRegion($region);
            $environment = $this->faker->randomElement(['dev', 'test', 'prod']);
            $purpose     = $this->faker->randomElement(['backup', 'migration', 'recovery']);
            $diskSnapshot->setTags([
                'Environment'  => \is_string($environment) ? $environment : 'dev',
                'SnapshotType' => $snapshotType instanceof SnapshotTypeEnum ? $snapshotType->value : 'disk',
                'Purpose'      => \is_string($purpose) ? $purpose : 'backup',
                'Size'         => \sprintf('%dGB', $sizeInGb),
            ]);
            $diskSnapshot->setSyncTime($this->generateSyncTime());
            $diskSnapshot->setCredential($credential);

            $manager->persist($diskSnapshot);
            $this->addReference(self::DISK_SNAPSHOT_REFERENCE_PREFIX . $i, $diskSnapshot);
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
