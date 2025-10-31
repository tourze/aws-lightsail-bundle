<?php

declare(strict_types=1);

namespace AwsLightsailBundle\DataFixtures;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Domain;
use AwsLightsailBundle\Entity\DomainEntry;
use AwsLightsailBundle\Enum\DnsRecordTypeEnum;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class DomainEntryFixtures extends AppFixtures implements DependentFixtureInterface
{
    public const DOMAIN_ENTRY_REFERENCE_PREFIX = 'domain_entry_';
    public const DOMAIN_ENTRY_COUNT            = 15;

    public function load(ObjectManager $manager): void
    {
        $credential = $this->getReference(AwsCredentialFixtures::CREDENTIAL_REFERENCE_PREFIX . 'default', AwsCredential::class);

        for ($i = 0; $i < self::DOMAIN_ENTRY_COUNT; ++$i) {
            $domainEntry = new DomainEntry();

            $domainIndex = $i % DomainFixtures::DOMAIN_COUNT;
            $domain      = $this->getReference(DomainFixtures::DOMAIN_REFERENCE_PREFIX . $domainIndex, Domain::class);
            $recordType  = $this->faker->randomElement(DnsRecordTypeEnum::cases());

            $recordName = match ($recordType) {
                DnsRecordTypeEnum::A     => $this->faker->randomElement(['@', 'www', 'api', 'cdn', 'mail']),
                DnsRecordTypeEnum::AAAA  => $this->faker->randomElement(['@', 'www', 'ipv6']),
                DnsRecordTypeEnum::CNAME => $this->faker->randomElement(['www', 'blog', 'shop', 'admin']),
                DnsRecordTypeEnum::MX    => '@',
                DnsRecordTypeEnum::TXT   => $this->faker->randomElement(['@', '_dmarc', '_domainkey']),
                DnsRecordTypeEnum::NS    => '@',
                DnsRecordTypeEnum::SRV   => (function (): string {
                    $service = $this->faker->randomElement(['sip', 'xmpp', 'ldap']);
                    \assert(\is_string($service));

                    return \sprintf('_%s._tcp', $service);
                })(),
                default                  => '@',
            };

            $recordValue = match ($recordType) {
                DnsRecordTypeEnum::A     => $this->faker->ipv4(),
                DnsRecordTypeEnum::AAAA  => $this->faker->ipv6(),
                DnsRecordTypeEnum::CNAME => \sprintf('%s.%s', $this->faker->domainWord(), $this->faker->domainName()),
                DnsRecordTypeEnum::MX    => \sprintf('%d %s', $this->faker->numberBetween(10, 50), $this->faker->domainName()),
                DnsRecordTypeEnum::TXT   => match ($recordName) {
                    '_dmarc'     => 'v=DMARC1; p=quarantine; rua=mailto:dmarc@' . $domain->getName(),
                    '_domainkey' => 'v=DKIM1; k=rsa; p=' . $this->faker->sha256(),
                    default      => $this->faker->text(100),
                },
                DnsRecordTypeEnum::NS  => \sprintf('ns%d.%s', $this->faker->numberBetween(1, 4), $this->faker->domainName()),
                DnsRecordTypeEnum::SRV => \sprintf('%d %d %d %s', $this->faker->numberBetween(0, 10), $this->faker->numberBetween(0, 10), $this->faker->numberBetween(1, 65535), $this->faker->domainName()),
                default                => $this->faker->ipv4(),
            };

            $domainEntry->setName(\is_string($recordName) ? $recordName : '@');
            $domainEntry->setType($recordType instanceof DnsRecordTypeEnum ? $recordType : DnsRecordTypeEnum::A);
            $domainEntry->setValue($recordValue);
            $ttl = match ($recordType) {
                DnsRecordTypeEnum::A, DnsRecordTypeEnum::AAAA => $this->faker->randomElement([300, 600, 1800, 3600]),
                DnsRecordTypeEnum::CNAME => $this->faker->randomElement([300, 1800, 3600]),
                DnsRecordTypeEnum::MX    => $this->faker->randomElement([1800, 3600, 7200]),
                DnsRecordTypeEnum::TXT   => $this->faker->randomElement([300, 600, 3600]),
                DnsRecordTypeEnum::NS    => $this->faker->randomElement([3600, 7200, 86400]),
                DnsRecordTypeEnum::SRV   => $this->faker->randomElement([600, 1800, 3600]),
                default                  => 3600,
            };
            $domainEntry->setTtl(\is_int($ttl) ? $ttl : null);
            $domainEntry->setIsAlias(DnsRecordTypeEnum::CNAME === $recordType && $this->faker->boolean(80));
            $domainEntry->setPriority(DnsRecordTypeEnum::MX === $recordType || DnsRecordTypeEnum::SRV === $recordType ? $this->faker->numberBetween(10, 100) : null);
            $domainEntry->setDomain($domain);
            $domainEntry->setSyncTime($this->generateSyncTime());

            $manager->persist($domainEntry);
            $this->addReference(self::DOMAIN_ENTRY_REFERENCE_PREFIX . $i, $domainEntry);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AwsCredentialFixtures::class,
            DomainFixtures::class,
        ];
    }
}
