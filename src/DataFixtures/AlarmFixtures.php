<?php

declare(strict_types=1);

namespace AwsLightsailBundle\DataFixtures;

use AwsLightsailBundle\Entity\Alarm;
use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Enum\AlarmMetricEnum;
use AwsLightsailBundle\Enum\AlarmStateEnum;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AlarmFixtures extends AppFixtures implements DependentFixtureInterface
{
    public const ALARM_REFERENCE_PREFIX = 'alarm_';
    public const ALARM_COUNT            = 10;

    public function load(ObjectManager $manager): void
    {
        $credential = $this->getReference(AwsCredentialFixtures::CREDENTIAL_REFERENCE_PREFIX . 'default', AwsCredential::class);

        for ($i = 0; $i < self::ALARM_COUNT; ++$i) {
            $alarm = new Alarm();

            $resourceName = $this->generateResourceName();
            $region       = $this->generateAwsRegion();
            $metricName   = $this->faker->randomElement(AlarmMetricEnum::cases());
            $state        = $this->faker->randomElement(AlarmStateEnum::cases());

            \assert($metricName instanceof AlarmMetricEnum);
            \assert($state instanceof AlarmStateEnum);

            $alarm->setName(\sprintf('%s-%s告警', $resourceName, $metricName->value));
            $alarm->setArn($this->generateAwsArn('lightsail', $region, 'alarm', $resourceName . '-alarm'));
            $alarm->setResourceName($resourceName);
            $resourceType = $this->faker->randomElement(['Instance', 'LoadBalancer', 'Database']);
            \assert(\is_string($resourceType));
            $alarm->setResourceType($resourceType);
            $alarm->setMetricName($metricName);
            $alarm->setState($state);
            $alarm->setRegion($region);
            $comparisonOperator = $this->faker->randomElement(['GreaterThanOrEqualToThreshold', 'LessThanThreshold']);
            \assert(\is_string($comparisonOperator));
            $alarm->setComparisonOperator($comparisonOperator);
            $alarm->setEvaluationPeriods((string) $this->faker->numberBetween(1, 5));
            $alarm->setThreshold($this->faker->randomFloat(2, 0.1, 100.0));
            $treatMissingData = $this->faker->randomElement(['breaching', 'notBreaching', 'ignore', null]);
            \assert(\is_string($treatMissingData) || null === $treatMissingData);
            $alarm->setTreatMissingData($treatMissingData);
            /** @var array<int, string>|null $contactProtocols */
            $contactProtocols = $this->faker->randomElement([
                ['Email'],
                ['SMS'],
                ['Email', 'SMS'],
                null,
            ]);
            $alarm->setContactProtocols($contactProtocols);
            $alarm->setMonitoredResourceInfo([
                'resourceType' => 'Instance',
                'resourceName' => $resourceName,
                'region'       => $region,
            ]);
            $alarm->setDatapointsToAlarm(null);
            $alarm->setNotificationEnabled($this->faker->boolean(80));
            $alarm->setNotificationTriggeredTime($this->faker->boolean(30) ? $this->generateSyncTime() : null);
            $alarm->setSyncTime($this->generateSyncTime());
            $alarm->setCredential($credential);

            $manager->persist($alarm);
            $this->addReference(self::ALARM_REFERENCE_PREFIX . $i, $alarm);
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
