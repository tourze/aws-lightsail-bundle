<?php

declare(strict_types=1);

namespace AwsLightsailBundle\DataFixtures;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Database;
use AwsLightsailBundle\Enum\DatabaseEngineEnum;
use AwsLightsailBundle\Enum\DatabaseStatusEnum;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class DatabaseFixtures extends AppFixtures implements DependentFixtureInterface
{
    public const DATABASE_REFERENCE_PREFIX = 'database_';
    public const DATABASE_COUNT            = 6;

    public function load(ObjectManager $manager): void
    {
        $credential = $this->getReference(AwsCredentialFixtures::CREDENTIAL_REFERENCE_PREFIX . 'default', AwsCredential::class);

        for ($i = 0; $i < self::DATABASE_COUNT; ++$i) {
            $database = $this->createDatabase($credential);
            $manager->persist($database);
            $this->addReference(self::DATABASE_REFERENCE_PREFIX . $i, $database);
        }

        $manager->flush();
    }

    private function createDatabase(AwsCredential $credential): Database
    {
        $database     = new Database();
        $resourceName = $this->generateResourceName();
        $region       = $this->generateAwsRegion();
        $engine       = $this->getRandomEngine();
        $status       = $this->getRandomStatus();

        $this->configureBasicDatabaseProperties($database, $resourceName, $region, $engine, $status);
        $this->configureEndpoints($database, $resourceName, $region, $status);
        $this->configureMaintenanceWindows($database);
        $this->configureAdditionalSettings($database, $engine, $status);
        $database->setSyncTime($this->generateSyncTime());
        $database->setCredential($credential);

        return $database;
    }

    private function getRandomEngine(): DatabaseEngineEnum
    {
        $engine = $this->faker->randomElement(DatabaseEngineEnum::cases());

        return $engine instanceof DatabaseEngineEnum ? $engine : DatabaseEngineEnum::MYSQL;
    }

    private function getRandomStatus(): DatabaseStatusEnum
    {
        $status = $this->faker->randomElement(DatabaseStatusEnum::cases());

        return $status instanceof DatabaseStatusEnum ? $status : DatabaseStatusEnum::AVAILABLE;
    }

    private function configureBasicDatabaseProperties(Database $database, string $resourceName, string $region, DatabaseEngineEnum $engine, DatabaseStatusEnum $status): void
    {
        $database->setName(\sprintf('%s-db', $resourceName));
        $database->setArn($this->generateAwsArn('lightsail', $region, 'relational-database', $resourceName));
        $database->setEngine($engine);
        $database->setEngineVersion($this->getEngineVersion($engine));
        $database->setMasterUsername($this->getMasterUsername());
        $database->setMasterPort($this->getDatabasePort($engine));
        $database->setStatus($status);
        $database->setRegion($region);
    }

    private function getEngineVersion(DatabaseEngineEnum $engine): string
    {
        $versions = DatabaseEngineEnum::MYSQL === $engine
            ? ['8.0.28', '8.0.31', '5.7.38']
            : ['13.7', '14.6', '15.1'];

        $version = $this->faker->randomElement($versions);

        return \is_string($version) ? $version : '1.0.0';
    }

    private function getMasterUsername(): string
    {
        $username = $this->faker->randomElement(['admin', 'root', 'dbadmin', 'master']);

        return \is_string($username) ? $username : 'admin';
    }

    private function getDatabasePort(DatabaseEngineEnum $engine): int
    {
        if (DatabaseEngineEnum::POSTGRES === $engine) {
            return 5432;
        }

        return 3306;
    }

    private function configureEndpoints(Database $database, string $resourceName, string $region, DatabaseStatusEnum $status): void
    {
        $database->setMasterEndpoint($this->generateMasterEndpoint($resourceName, $region, $status));
        $database->setSecondaryEndpoint($this->generateSecondaryEndpoint($resourceName, $region, $status));
    }

    private function generateMasterEndpoint(string $resourceName, string $region, DatabaseStatusEnum $status): ?string
    {
        if (DatabaseStatusEnum::AVAILABLE !== $status) {
            return null;
        }

        $suffix = $this->generateRandomLetterPair();

        return \sprintf('%s.%s.%s.rds.amazonaws.com', $resourceName, $suffix, $region);
    }

    private function generateSecondaryEndpoint(string $resourceName, string $region, DatabaseStatusEnum $status): ?string
    {
        if (!$this->faker->boolean(30) || DatabaseStatusEnum::AVAILABLE !== $status) {
            return null;
        }

        $suffix = $this->generateRandomLetterPair();

        return \sprintf('%s-ro.%s.%s.rds.amazonaws.com', $resourceName, $suffix, $region);
    }

    private function generateRandomLetterPair(): string
    {
        $letter1 = $this->faker->randomLetter();
        $letter2 = $this->faker->randomLetter();

        return (\is_string($letter1) ? $letter1 : 'a') . (\is_string($letter2) ? $letter2 : 'b');
    }

    private function configureMaintenanceWindows(Database $database): void
    {
        $database->setPreferredBackupWindow($this->generateBackupWindow());
        $database->setPreferredMaintenanceWindow($this->generateMaintenanceWindow());
    }

    private function generateBackupWindow(): string
    {
        $startMinute = $this->faker->randomElement([0, 30]);
        $endMinute   = $this->faker->randomElement([0, 30]);

        return \sprintf(
            '%02d:%02d-%02d:%02d',
            $this->faker->numberBetween(0, 23),
            \is_int($startMinute) ? $startMinute : 0,
            $this->faker->numberBetween(0, 23),
            \is_int($endMinute) ? $endMinute : 0
        );
    }

    private function generateMaintenanceWindow(): string
    {
        $startDay    = $this->faker->dayOfWeek();
        $endDay      = $this->faker->dayOfWeek();
        $startMinute = $this->faker->randomElement([0, 30]);
        $endMinute   = $this->faker->randomElement([0, 30]);

        return \sprintf(
            '%s:%02d:%02d-%s:%02d:%02d',
            \is_string($startDay) ? $startDay : 'Mon',
            $this->faker->numberBetween(0, 23),
            \is_int($startMinute) ? $startMinute : 0,
            \is_string($endDay) ? $endDay : 'Mon',
            $this->faker->numberBetween(0, 23),
            \is_int($endMinute) ? $endMinute : 0
        );
    }

    private function configureAdditionalSettings(Database $database, DatabaseEngineEnum $engine, DatabaseStatusEnum $status): void
    {
        $database->setPubliclyAccessible($this->faker->boolean(20));
        $database->setSupportCode($this->faker->boolean(60));
        $database->setCaCertificateIdentifier($this->generateCaCertificate());
        $database->setPendingModifiedValues($this->generatePendingModifiedValues());
        $database->setBackupRetentionEnabled($this->faker->boolean(70));
        $database->setTags($this->generateDatabaseTags($engine));
        $database->setBundleId($this->getBundleId());
        $database->setAutoMinorVersionUpgrade($this->faker->boolean(80));
    }

    private function generateCaCertificate(): ?string
    {
        if (!$this->faker->boolean(80)) {
            return null;
        }

        return \sprintf('rds-ca-%d', $this->faker->numberBetween(2019, 2024));
    }

    /**
     * @return array<string, mixed>|null
     */
    private function generatePendingModifiedValues(): ?array
    {
        if (!$this->faker->boolean(20)) {
            return null;
        }

        return [
            'masterUsername'        => 'newadmin',
            'allocatedStorage'      => $this->faker->numberBetween(20, 100),
            'backupRetentionPeriod' => $this->faker->numberBetween(1, 35),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function generateDatabaseTags(DatabaseEngineEnum $engine): array
    {
        $environment = $this->faker->randomElement(['dev', 'test', 'prod']);
        $purpose     = $this->faker->randomElement(['web', 'api', 'cache', 'analytics']);

        return [
            'Environment' => \is_string($environment) ? $environment : 'dev',
            'Database'    => $engine->value,
            'Purpose'     => \is_string($purpose) ? $purpose : 'web',
        ];
    }

    private function getBundleId(): string
    {
        $bundleId = $this->faker->randomElement(['db.t3.micro', 'db.t3.small', 'db.t3.medium', 'db.t3.large']);

        return \is_string($bundleId) ? $bundleId : 'db.t3.micro';
    }

    public function getDependencies(): array
    {
        return [
            AwsCredentialFixtures::class,
        ];
    }
}
