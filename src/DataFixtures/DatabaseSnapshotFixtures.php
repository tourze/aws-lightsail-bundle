<?php

declare(strict_types=1);

namespace AwsLightsailBundle\DataFixtures;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\DatabaseSnapshot;
use AwsLightsailBundle\Enum\DatabaseEngineEnum;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class DatabaseSnapshotFixtures extends AppFixtures implements DependentFixtureInterface
{
    public const DATABASE_SNAPSHOT_REFERENCE_PREFIX = 'database_snapshot_';
    public const DATABASE_SNAPSHOT_COUNT            = 7;

    public function load(ObjectManager $manager): void
    {
        $credential = $this->getReference(AwsCredentialFixtures::CREDENTIAL_REFERENCE_PREFIX . 'default', AwsCredential::class);

        for ($i = 0; $i < self::DATABASE_SNAPSHOT_COUNT; ++$i) {
            $databaseSnapshot = new DatabaseSnapshot();

            $resourceName  = $this->generateResourceName();
            $region        = $this->generateAwsRegion();
            $engine        = $this->faker->randomElement(DatabaseEngineEnum::cases());
            $engineVersion = match ($engine) {
                DatabaseEngineEnum::MYSQL    => $this->faker->randomElement(['8.0.28', '8.0.31', '5.7.38']),
                DatabaseEngineEnum::POSTGRES => $this->faker->randomElement(['13.7', '14.6', '15.1']),
                default                      => '1.0.0',
            };

            $databaseSnapshot->setName(\sprintf('%s-snapshot-%s', $resourceName, $this->faker->date('Y-m-d')));
            $databaseSnapshot->setArn($this->generateAwsArn('lightsail', $region, 'relational-database-snapshot', $resourceName . '-snapshot'));
            $databaseSnapshot->setDatabaseName(\sprintf('%s-db', $resourceName));
            $databaseSnapshot->setEngine($engine instanceof DatabaseEngineEnum ? $engine : DatabaseEngineEnum::MYSQL);
            $databaseSnapshot->setEngineVersion(\is_string($engineVersion) ? $engineVersion : '1.0.0');
            $databaseSnapshot->setSizeInGb($this->faker->numberBetween(20, 1000));
            $databaseSnapshot->setRegion($region);
            $databaseSnapshot->setTags([
                'BundleId'     => $this->faker->randomElement(['db.t3.micro', 'db.t3.small', 'db.t3.medium', 'db.t3.large']),
                'Environment'  => $this->faker->randomElement(['dev', 'test', 'prod']),
                'SnapshotType' => $this->faker->randomElement(['manual', 'automated']),
                'Database'     => $engine instanceof DatabaseEngineEnum ? $engine->value : 'mysql',
                'Purpose'      => $this->faker->randomElement(['backup', 'migration', 'test']),
            ]);
            $databaseSnapshot->setSyncTime($this->generateSyncTime());
            $databaseSnapshot->setCredential($credential);

            $manager->persist($databaseSnapshot);
            $this->addReference(self::DATABASE_SNAPSHOT_REFERENCE_PREFIX . $i, $databaseSnapshot);
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
