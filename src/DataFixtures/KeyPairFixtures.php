<?php

declare(strict_types=1);

namespace AwsLightsailBundle\DataFixtures;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\KeyPair;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class KeyPairFixtures extends AppFixtures implements DependentFixtureInterface
{
    public const KEY_PAIR_REFERENCE_PREFIX = 'key_pair_';
    public const KEY_PAIR_COUNT            = 5;

    public function load(ObjectManager $manager): void
    {
        $credential = $this->getReference(AwsCredentialFixtures::CREDENTIAL_REFERENCE_PREFIX . 'default', AwsCredential::class);

        for ($i = 0; $i < self::KEY_PAIR_COUNT; ++$i) {
            $keyPair = new KeyPair();

            $resourceName = $this->generateResourceName();
            $region       = $this->generateAwsRegion();

            $keyPair->setName(\sprintf('%s-keypair', $resourceName));
            $keyPair->setArn($this->generateAwsArn('lightsail', $region, 'key-pair', $resourceName));
            $keyPair->setFingerprint(\sprintf(
                '%s:%s:%s:%s:%s',
                $this->faker->regexify('[a-f0-9]{2}'),
                $this->faker->regexify('[a-f0-9]{2}'),
                $this->faker->regexify('[a-f0-9]{2}'),
                $this->faker->regexify('[a-f0-9]{2}'),
                $this->faker->regexify('[a-f0-9]{2}')
            ));
            $keyPair->setPublicKey(\sprintf(
                'ssh-rsa %s %s@%s',
                \base64_encode($this->faker->sha256()),
                $this->faker->userName(),
                $this->faker->domainName()
            ));
            $keyPair->setPrivateKey($this->faker->boolean(50) ? \sprintf(
                "-----BEGIN RSA PRIVATE KEY-----\n%s\n-----END RSA PRIVATE KEY-----",
                \base64_encode($this->faker->text(1024))
            ) : null);
            $keyPair->setIsEncrypted($this->faker->boolean(30));
            $keyPair->setRegion($region);
            $environment = $this->faker->randomElement(['dev', 'test', 'prod']);
            $purpose     = $this->faker->randomElement(['ssh', 'deployment', 'admin']);
            $owner       = $this->faker->userName();
            $keyPair->setTags([
                'Environment' => \is_string($environment) ? $environment : 'dev',
                'Purpose'     => \is_string($purpose) ? $purpose : 'ssh',
                'Owner'       => \is_string($owner) ? $owner : 'admin',
            ]);
            $keyPair->setSyncTime($this->generateSyncTime());
            $keyPair->setCredential($credential);

            $manager->persist($keyPair);
            $this->addReference(self::KEY_PAIR_REFERENCE_PREFIX . $i, $keyPair);
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
