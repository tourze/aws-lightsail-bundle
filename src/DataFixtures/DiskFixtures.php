<?php

declare(strict_types=1);

namespace AwsLightsailBundle\DataFixtures;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Disk;
use AwsLightsailBundle\Enum\DiskStateEnum;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class DiskFixtures extends AppFixtures implements DependentFixtureInterface
{
    public const DISK_REFERENCE_PREFIX = 'disk_';
    public const DISK_COUNT            = 8;

    public function load(ObjectManager $manager): void
    {
        $credential = $this->getReference(AwsCredentialFixtures::CREDENTIAL_REFERENCE_PREFIX . 'default', AwsCredential::class);

        for ($i = 0; $i < self::DISK_COUNT; ++$i) {
            $disk = $this->createDisk($credential);
            $manager->persist($disk);
            $this->addReference(self::DISK_REFERENCE_PREFIX . $i, $disk);
        }

        $manager->flush();
    }

    private function createDisk(AwsCredential $credential): Disk
    {
        $disk         = new Disk();
        $resourceName = $this->generateResourceName();
        $region       = $this->generateAwsRegion();
        $state        = $this->getRandomState();
        $sizeInGb     = $this->getDiskSize();

        $this->configureDiskProperties($disk, $resourceName, $region, $state, $sizeInGb);
        $disk->setTags($this->generateDiskTags($sizeInGb));
        $disk->setSyncTime($this->generateSyncTime());
        $disk->setCredential($credential);

        return $disk;
    }

    private function getRandomState(): DiskStateEnum
    {
        $state = $this->faker->randomElement(DiskStateEnum::cases());

        return $state instanceof DiskStateEnum ? $state : DiskStateEnum::AVAILABLE;
    }

    private function getDiskSize(): int
    {
        $sizeInGb = $this->faker->randomElement([8, 20, 40, 80, 160, 320, 640]);

        return \is_int($sizeInGb) ? $sizeInGb : 20;
    }

    private function configureDiskProperties(Disk $disk, string $resourceName, string $region, DiskStateEnum $state, int $sizeInGb): void
    {
        $disk->setName(\sprintf('%s-disk', $resourceName));
        $disk->setArn($this->generateAwsArn('lightsail', $region, 'disk', $resourceName));
        $disk->setAttachedTo($this->getAttachedInstance($state, $resourceName));
        $disk->setAttachmentState($this->getAttachmentState($state));
        $disk->setIsSystemDisk($this->faker->boolean(20));
        $disk->setState($state);
        $disk->setRegion($region);
        $disk->setSizeInGb($sizeInGb);
        $disk->setIops($this->faker->numberBetween(100, 10000));
        $disk->setPath($this->getDiskPath($state));
    }

    private function getAttachedInstance(DiskStateEnum $state, string $resourceName): ?string
    {
        if (DiskStateEnum::IN_USE !== $state) {
            return null;
        }

        return \sprintf('%s-instance', $resourceName);
    }

    private function getAttachmentState(DiskStateEnum $state): ?string
    {
        return match ($state) {
            DiskStateEnum::IN_USE    => 'attached',
            DiskStateEnum::AVAILABLE => 'detached',
            default                  => null,
        };
    }

    private function getDiskPath(DiskStateEnum $state): ?string
    {
        if (DiskStateEnum::IN_USE !== $state) {
            return null;
        }

        $path = $this->faker->randomElement(['/dev/xvdf', '/dev/xvdg', '/dev/xvdh']);

        return \is_string($path) ? $path : null;
    }

    /**
     * @return array<string, string>
     */
    private function generateDiskTags(int $sizeInGb): array
    {
        $environment = $this->faker->randomElement(['dev', 'test', 'prod']);
        $purpose     = $this->faker->randomElement(['data', 'backup', 'temp', 'logs']);

        return [
            'Environment' => \is_string($environment) ? $environment : 'dev',
            'Purpose'     => \is_string($purpose) ? $purpose : 'data',
            'Size'        => \sprintf('%dGB', $sizeInGb),
        ];
    }

    public function getDependencies(): array
    {
        return [
            AwsCredentialFixtures::class,
        ];
    }
}
