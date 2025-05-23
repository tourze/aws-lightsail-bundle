<?php

namespace AwsLightsailBundle\Entity;

use AwsLightsailBundle\Enum\DnsRecordTypeEnum;
use AwsLightsailBundle\Repository\DomainEntryRepository;
use Carbon\Carbon;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;

#[ORM\Entity(repositoryClass: DomainEntryRepository::class)]
#[ORM\Table(name: 'aws_lightsail_domain_entry', options: ['comment' => 'AWS Lightsail 域名记录表'])]
class DomainEntry implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '记录名称'])]
    private string $name;

    #[ORM\Column(type: 'string', length: 50, enumType: DnsRecordTypeEnum::class, options: ['comment' => '记录类型'])]
    private DnsRecordTypeEnum $type;

    #[ORM\Column(type: 'text', options: ['comment' => '记录值'])]
    private string $value;

    #[ORM\Column(type: 'integer', nullable: true, options: ['comment' => 'TTL'])]
    private ?int $ttl = null;

    #[ORM\Column(type: 'boolean', options: ['comment' => '是否为别名'])]
    private bool $isAlias = false;

    #[ORM\Column(type: 'integer', nullable: true, options: ['comment' => '优先级'])]
    private ?int $priority = null;

    #[ORM\ManyToOne(targetEntity: Domain::class, inversedBy: 'entries')]
    #[ORM\JoinColumn(nullable: false)]
    private Domain $domain;

    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['comment' => '创建时间'])]
    private \DateTimeInterface $createTime;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '同步时间'])]
    private ?\DateTimeInterface $syncTime = null;
    
    #[UpdateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

    public function __construct()
    {
        $this->createTime = Carbon::now();
    }

    public function __toString(): string
    {
        return sprintf('DomainEntry %s (%s)', $this->name, $this->type->value);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getType(): DnsRecordTypeEnum
    {
        return $this->type;
    }

    public function setType(DnsRecordTypeEnum $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function getTtl(): ?int
    {
        return $this->ttl;
    }

    public function setTtl(?int $ttl): self
    {
        $this->ttl = $ttl;
        return $this;
    }

    public function isAlias(): bool
    {
        return $this->isAlias;
    }

    public function setIsAlias(bool $isAlias): self
    {
        $this->isAlias = $isAlias;
        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): self
    {
        $this->priority = $priority;
        return $this;
    }

    public function getDomain(): Domain
    {
        return $this->domain;
    }

    public function setDomain(?Domain $domain): self
    {
        $this->domain = $domain;
        return $this;
    }

    public function getCreateTime(): \DateTimeInterface
    {
        return $this->createTime;
    }

    public function getSyncTime(): ?\DateTimeInterface
    {
        return $this->syncTime;
    }

    public function setSyncTime(?\DateTimeInterface $syncTime): self
    {
        $this->syncTime = $syncTime;
        return $this;
    }
    
    public function getUpdateTime(): ?\DateTimeInterface
    {
        return $this->updateTime;
    }

    public function setUpdateTime(?\DateTimeInterface $updateTime): self
    {
        $this->updateTime = $updateTime;
        return $this;
    }
}
