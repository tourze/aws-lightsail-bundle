<?php

declare(strict_types=1);

namespace AwsLightsailBundle\DataFixtures;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\ContactMethod;
use AwsLightsailBundle\Enum\ContactMethodStatusEnum;
use AwsLightsailBundle\Enum\ContactMethodTypeEnum;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ContactMethodFixtures extends AppFixtures implements DependentFixtureInterface
{
    public const CONTACT_METHOD_REFERENCE_PREFIX = 'contact_method_';
    public const CONTACT_METHOD_COUNT            = 5;

    public function load(ObjectManager $manager): void
    {
        $credential = $this->getReference(AwsCredentialFixtures::CREDENTIAL_REFERENCE_PREFIX . 'default', AwsCredential::class);

        for ($i = 0; $i < self::CONTACT_METHOD_COUNT; ++$i) {
            $contactMethod = new ContactMethod();

            $type            = $this->faker->randomElement(ContactMethodTypeEnum::cases());
            $contactEndpoint = $this->generateContactEndpoint($type instanceof ContactMethodTypeEnum ? $type : ContactMethodTypeEnum::EMAIL);

            $region = $this->generateAwsRegion();

            $contactMethod->setName(($type instanceof ContactMethodTypeEnum ? $type->value : 'unknown') . '-' . $this->faker->word());
            $contactMethod->setArn($this->generateAwsArn('lightsail', $region, 'contact-method', 'contact-' . $i));
            $contactMethod->setType($type instanceof ContactMethodTypeEnum ? $type : ContactMethodTypeEnum::EMAIL);
            $status = $this->faker->randomElement(ContactMethodStatusEnum::cases());
            $contactMethod->setStatus($status instanceof ContactMethodStatusEnum ? $status : ContactMethodStatusEnum::VERIFIED);
            $contactMethod->setContactEndpoint($contactEndpoint);
            $contactMethod->setRegion($region);
            $protocol = $this->faker->randomElement(['HTTPS', 'HTTP']);
            $contactMethod->setProtocol(\is_string($protocol) ? $protocol : null);
            $contactMethod->setSyncTime($this->generateSyncTime());
            $contactMethod->setCredential($credential);

            $manager->persist($contactMethod);
            $this->addReference(self::CONTACT_METHOD_REFERENCE_PREFIX . $i, $contactMethod);
        }

        $manager->flush();
    }

    private function generateContactEndpoint(ContactMethodTypeEnum $type): string
    {
        return match ($type) {
            ContactMethodTypeEnum::EMAIL => $this->faker->email(),
            ContactMethodTypeEnum::SMS   => '+86' . $this->faker->numerify('###########'),
        };
    }

    public function getDependencies(): array
    {
        return [
            AwsCredentialFixtures::class,
        ];
    }
}
