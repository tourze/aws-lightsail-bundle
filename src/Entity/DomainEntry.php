<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Entity;

use AwsLightsailBundle\Enum\DnsRecordTypeEnum;
use AwsLightsailBundle\Repository\DomainEntryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: DomainEntryRepository::class)]
#[ORM\Table(name: 'aws_lightsail_domain_entry', options: ['comment' => 'AWS Lightsail 域名记录表'])]
class DomainEntry implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '主键ID'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '记录名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 50, enumType: DnsRecordTypeEnum::class, options: ['comment' => '记录类型'])]
    #[Assert\Choice(callback: [DnsRecordTypeEnum::class, 'cases'])]
    #[Assert\NotBlank]
    private DnsRecordTypeEnum $type;

    #[ORM\Column(type: Types::TEXT, options: ['comment' => '记录值'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 65535)]
    private string $value;

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => 'TTL'])]
    #[Assert\Type(type: 'int')]
    #[Assert\PositiveOrZero]
    private ?int $ttl = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否为别名'])]
    #[Assert\Type(type: 'bool')]
    private bool $isAlias = false;

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '优先级'])]
    #[Assert\Type(type: 'int')]
    #[Assert\PositiveOrZero]
    private ?int $priority = null;

    #[ORM\ManyToOne(targetEntity: Domain::class, inversedBy: 'entries')]
    #[ORM\JoinColumn(nullable: false)]
    private Domain $domain;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '同步时间'])]
    #[Assert\Type(type: '\DateTimeInterface')]
    private ?\DateTimeImmutable $syncTime = null;

    public function __toString(): string
    {
        return \sprintf('DomainEntry %s (%s)', $this->name, $this->type->value);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getType(): DnsRecordTypeEnum
    {
        return $this->type;
    }

    public function setType(DnsRecordTypeEnum $type): void
    {
        $this->type = $type;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function getTtl(): ?int
    {
        return $this->ttl;
    }

    public function setTtl(?int $ttl): void
    {
        $this->ttl = $ttl;
    }

    public function isAlias(): bool
    {
        return $this->isAlias;
    }

    public function setIsAlias(bool $isAlias): void
    {
        $this->isAlias = $isAlias;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): void
    {
        $this->priority = $priority;
    }

    public function getDomain(): Domain
    {
        return $this->domain;
    }

    public function setDomain(Domain $domain): void
    {
        $this->domain = $domain;
    }

    public function getSyncTime(): ?\DateTimeImmutable
    {
        return $this->syncTime;
    }

    public function setSyncTime(?\DateTimeInterface $syncTime): void
    {
        if (null !== $syncTime && !$syncTime instanceof \DateTimeImmutable) {
            $syncTime = \DateTimeImmutable::createFromInterface($syncTime);
        }
        $this->syncTime = $syncTime;
    }
}
