<?php

declare(strict_types=1);

namespace AwsLightsailBundle\DataFixtures;

use AwsLightsailBundle\Enum\AmazonRegion;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
abstract class AppFixtures extends Fixture
{
    protected Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('zh_CN');
    }

    abstract public function load(ObjectManager $manager): void;

    protected function generateAwsArn(string $service, string $region, string $resourceType, string $resourceName): string
    {
        return \sprintf('arn:aws:%s:%s:123456789012:%s/%s', $service, $region, $resourceType, $resourceName);
    }

    protected function generateAwsRegion(): string
    {
        $region = $this->faker->randomElement(AmazonRegion::cases());
        \assert($region instanceof AmazonRegion);

        return $region->value;
    }

    protected function generateResourceName(): string
    {
        $prefix = $this->faker->randomElement(['test', 'dev', 'prod', 'staging']);
        \assert(\is_string($prefix));
        $name   = $this->faker->word();
        $suffix = $this->faker->numberBetween(1, 999);

        return \sprintf('%s-%s-%d', $prefix, $name, $suffix);
    }

    protected function generateSyncTime(): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromMutable(
            $this->faker->dateTimeBetween('-30 days', 'now')
        );
    }
}
