<?php

declare(strict_types=1);

namespace AwsLightsailBundle\DataFixtures;

use AwsLightsailBundle\Entity\AwsCredential;
use Doctrine\Persistence\ObjectManager;

class AwsCredentialFixtures extends AppFixtures
{
    public const CREDENTIAL_REFERENCE_PREFIX = 'aws_credential_';
    public const CREDENTIAL_COUNT            = 5;

    public function load(ObjectManager $manager): void
    {
        // 创建默认凭证
        $defaultCredential = new AwsCredential();
        $defaultCredential->setName('默认凭证');
        $defaultCredential->setAccessKeyId('AKIAI' . \strtoupper($this->faker->bothify('?????#####?????')));
        $defaultCredential->setSecretAccessKey($this->faker->bothify('????????????????????????????????????????'));
        $defaultCredential->setIsDefault(true);

        $manager->persist($defaultCredential);
        $this->addReference(self::CREDENTIAL_REFERENCE_PREFIX . 'default', $defaultCredential);

        // 创建其他测试凭证
        for ($i = 0; $i < self::CREDENTIAL_COUNT - 1; ++$i) {
            $credential = new AwsCredential();
            $credential->setName($this->faker->word . ' ' . $this->faker->word . '-凭证');
            $credential->setAccessKeyId('AKIAI' . \strtoupper($this->faker->bothify('?????#####?????')));
            $credential->setSecretAccessKey($this->faker->bothify('????????????????????????????????????????'));
            $credential->setIsDefault(false);

            $manager->persist($credential);
            $this->addReference(self::CREDENTIAL_REFERENCE_PREFIX . $i, $credential);
        }

        $manager->flush();
    }
}
