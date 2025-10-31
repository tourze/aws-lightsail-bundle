<?php

declare(strict_types=1);

namespace AwsLightsailBundle\DataFixtures;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Bucket;
use AwsLightsailBundle\Enum\BucketAccessRuleEnum;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class BucketFixtures extends AppFixtures implements DependentFixtureInterface
{
    public const BUCKET_REFERENCE_PREFIX = 'bucket_';
    public const BUCKET_COUNT            = 8;

    public function load(ObjectManager $manager): void
    {
        $credential = $this->getReference(AwsCredentialFixtures::CREDENTIAL_REFERENCE_PREFIX . 'default', AwsCredential::class);

        for ($i = 0; $i < self::BUCKET_COUNT; ++$i) {
            $bucket = new Bucket();

            $bucketName = \strtolower($this->generateResourceName());
            $region     = $this->generateAwsRegion();

            $bucket->setName($bucketName);
            $bucket->setArn($this->generateAwsArn('s3', $region, 'bucket', $bucketName));
            $bucket->setUrl(\sprintf('https://%s.s3.%s.amazonaws.com', $bucketName, $region));
            $bucket->setRegion($region);
            $bucket->setObjectCount($this->faker->numberBetween(0, 1000));
            $bucket->setSizeInMb($this->faker->numberBetween(0, 1000000));
            $bucket->setReadonlyAccessAccounts($this->faker->boolean(20) ? [$this->faker->word()] : null);
            $bucket->setIsResourceType($this->faker->boolean(50));
            $accessRules = $this->faker->randomElement(BucketAccessRuleEnum::cases());
            \assert($accessRules instanceof BucketAccessRuleEnum);
            $bucket->setAccessRules($accessRules);
            $bucket->setCorsRules($this->generateCorsRules());
            $bucket->setSyncTime($this->generateSyncTime());
            $bucket->setCredential($credential);

            $manager->persist($bucket);
            $this->addReference(self::BUCKET_REFERENCE_PREFIX . $i, $bucket);
        }

        $manager->flush();
    }

    /**
     * @return array<int, array{rule: string, allowedOrigins: string[]}>
     */
    private function generateCorsRules(): array
    {
        $rules     = [];
        $ruleCount = $this->faker->numberBetween(1, 3);

        for ($i = 0; $i < $ruleCount; ++$i) {
            $rules[] = [
                'rule' => (function () {
                    $rule = $this->faker->randomElement(BucketAccessRuleEnum::cases());
                    \assert($rule instanceof BucketAccessRuleEnum);

                    return $rule->value;
                })(),
                'allowedOrigins' => (function (): array {
                    /** @var string[] $origins */
                    $origins = $this->faker->randomElement([
                        ['*'],
                        ['https://test-domain.com'],
                        ['https://app.test-domain.com', 'https://api.test-domain.com'],
                    ]);

                    return $origins;
                })(),
            ];
        }

        return $rules;
    }

    public function getDependencies(): array
    {
        return [
            AwsCredentialFixtures::class,
        ];
    }
}
