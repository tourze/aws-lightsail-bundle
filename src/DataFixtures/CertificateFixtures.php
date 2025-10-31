<?php

declare(strict_types=1);

namespace AwsLightsailBundle\DataFixtures;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Certificate;
use AwsLightsailBundle\Enum\CertificateStatusEnum;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CertificateFixtures extends AppFixtures implements DependentFixtureInterface
{
    public const CERTIFICATE_REFERENCE_PREFIX = 'certificate_';
    public const CERTIFICATE_COUNT            = 6;

    public function load(ObjectManager $manager): void
    {
        $credential = $this->getReference(AwsCredentialFixtures::CREDENTIAL_REFERENCE_PREFIX . 'default', AwsCredential::class);

        for ($i = 0; $i < self::CERTIFICATE_COUNT; ++$i) {
            $certificate = new Certificate();

            $domainName = $this->faker->domainName();
            $region     = $this->generateAwsRegion();

            $certificate->setName($domainName . '-cert');
            $certificate->setArn($this->generateAwsArn('lightsail', $region, 'certificate', \str_replace('.', '-', $domainName)));
            $certificate->setDomainName($domainName);
            $certificate->setSubjectAlternativeNames($this->generateSubjectAlternativeNames($domainName));
            $status = $this->faker->randomElement(CertificateStatusEnum::cases());
            \assert($status instanceof CertificateStatusEnum);
            $certificate->setStatus($status);
            $certificate->setKeyAlgorithm(['algorithm' => 'RSA-2048']);
            $certificate->setSerialNumber($this->faker->bothify('##:##:##:##:##:##:##:##'));
            $certificate->setValidFromTime($this->faker->boolean(70) ? $this->generateSyncTime() : null);
            $certificate->setValidToTime($this->faker->boolean(70) ? \DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('+30 days', '+365 days')) : null);
            $certificate->setDomainValidationRecords($this->generateDomainValidationRecords($domainName));
            $certificate->setInUse($this->faker->boolean(60));
            $certificate->setIsManaged($this->faker->boolean(80));
            $certificate->setRegion($region);
            $certificate->setSyncTime($this->generateSyncTime());
            $certificate->setCredential($credential);

            $manager->persist($certificate);
            $this->addReference(self::CERTIFICATE_REFERENCE_PREFIX . $i, $certificate);
        }

        $manager->flush();
    }

    /**
     * @return string[]
     */
    private function generateSubjectAlternativeNames(string $domainName): array
    {
        $names = [$domainName];

        if ($this->faker->boolean(60)) {
            $names[] = 'www.' . $domainName;
        }

        if ($this->faker->boolean(30)) {
            $names[] = 'api.' . $domainName;
        }

        return $names;
    }

    /**
     * @return array<int, array{domainName: string, resourceRecord: array{name: string, type: string, value: string}}>
     */
    private function generateDomainValidationRecords(string $domainName): array
    {
        return [
            [
                'domainName'     => $domainName,
                'resourceRecord' => [
                    'name'  => '_' . $this->faker->bothify('????????????????') . '.' . $domainName,
                    'type'  => 'CNAME',
                    'value' => $this->faker->bothify('????????????????') . '.acm-validations.aws',
                ],
            ],
        ];
    }

    public function getDependencies(): array
    {
        return [
            AwsCredentialFixtures::class,
        ];
    }
}
