<?php

namespace AwsLightsailBundle\Entity;

use AwsLightsailBundle\Enum\DistributionStatusEnum;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;

#[ORM\Entity]
#[ORM\Table(name: 'aws_lightsail_distribution', options: ['comment' => 'AWS Lightsail CDN 分发表'])]
class Distribution implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '分发名称'])]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => 'AWS ARN'])]
    private string $arn;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '默认域名'])]
    private string $defaultDomainName;

    #[ORM\Column(type: 'string', length: 50, enumType: DistributionStatusEnum::class, options: ['comment' => '状态'])]
    private DistributionStatusEnum $status;

    #[ORM\Column(type: 'string', length: 50, options: ['comment' => 'AWS 区域'])]
    private string $region;

    #[ORM\Column(type: 'json', options: ['comment' => '源站配置'])]
    private array $originConfigs = [];

    #[ORM\Column(type: 'json', options: ['comment' => '默认缓存行为'])]
    private array $defaultCacheBehavior = [];

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => '缓存行为'])]
    private ?array $cacheBehaviors = null;

    #[ORM\Column(type: 'boolean', options: ['comment' => '是否启用'])]
    private bool $isEnabled = true;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['comment' => '证书名称'])]
    private ?string $certificateName = null;

    #[ORM\Column(type: 'boolean', options: ['comment' => '查看器协议策略'])]
    private bool $viewerProtocolPolicy = false;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => '标签'])]
    private ?array $tags = null;

    #[CreateTimeColumn]
    #[ORM\Column(type: 'datetime_immutable', options: ['comment' => '创建时间'])]
    private \DateTimeImmutable $createTime;

    #[ORM\Column(type: 'datetime_immutable', nullable: true, options: ['comment' => '同步时间'])]
    private ?\DateTimeImmutable $syncTime = null;

    #[ORM\ManyToOne(targetEntity: AwsCredential::class)]
    #[ORM\JoinColumn(nullable: false)]
    private AwsCredential $credential;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => '替代域名'])]
    private ?array $alternativeDomainNames = null;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => '源站公共DNS'])]
    private ?array $originPublicDNS = null;

    #[UpdateTimeColumn]
    #[ORM\Column(type: 'datetime_immutable', nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeImmutable $updateTime = null;

    public function __construct()
    {
        $this->createTime = new \DateTimeImmutable();
        $this->status = DistributionStatusEnum::PENDING;
    }

    public function __toString(): string
    {
        return sprintf('Distribution %s (%s)', $this->name, $this->status->value);
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

    public function getArn(): string
    {
        return $this->arn;
    }

    public function setArn(string $arn): self
    {
        $this->arn = $arn;
        return $this;
    }

    public function getDefaultDomainName(): string
    {
        return $this->defaultDomainName;
    }

    public function setDefaultDomainName(string $defaultDomainName): self
    {
        $this->defaultDomainName = $defaultDomainName;
        return $this;
    }

    public function getStatus(): DistributionStatusEnum
    {
        return $this->status;
    }

    public function setStatus(DistributionStatusEnum $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function setRegion(string $region): self
    {
        $this->region = $region;
        return $this;
    }

    public function getOriginConfigs(): array
    {
        return $this->originConfigs;
    }

    public function setOriginConfigs(array $originConfigs): self
    {
        $this->originConfigs = $originConfigs;
        return $this;
    }

    public function getDefaultCacheBehavior(): array
    {
        return $this->defaultCacheBehavior;
    }

    public function setDefaultCacheBehavior(array $defaultCacheBehavior): self
    {
        $this->defaultCacheBehavior = $defaultCacheBehavior;
        return $this;
    }

    public function getCacheBehaviors(): ?array
    {
        return $this->cacheBehaviors;
    }

    public function setCacheBehaviors(?array $cacheBehaviors): self
    {
        $this->cacheBehaviors = $cacheBehaviors;
        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): self
    {
        $this->isEnabled = $isEnabled;
        return $this;
    }

    public function getCertificateName(): ?string
    {
        return $this->certificateName;
    }

    public function setCertificateName(?string $certificateName): self
    {
        $this->certificateName = $certificateName;
        return $this;
    }

    public function getViewerProtocolPolicy(): bool
    {
        return $this->viewerProtocolPolicy;
    }

    public function setViewerProtocolPolicy(bool $viewerProtocolPolicy): self
    {
        $this->viewerProtocolPolicy = $viewerProtocolPolicy;
        return $this;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function setTags(?array $tags): self
    {
        $this->tags = $tags;
        return $this;
    }

    public function getCreateTime(): \DateTimeImmutable
    {
        return $this->createTime;
    }

    public function getSyncTime(): ?\DateTimeImmutable
    {
        return $this->syncTime;
    }

    public function setSyncTime(?\DateTimeImmutable $syncTime): self
    {
        $this->syncTime = $syncTime;
        return $this;
    }

    public function getCredential(): AwsCredential
    {
        return $this->credential;
    }

    public function setCredential(AwsCredential $credential): self
    {
        $this->credential = $credential;
        return $this;
    }

    public function getAlternativeDomainNames(): ?array
    {
        return $this->alternativeDomainNames;
    }

    public function setAlternativeDomainNames(?array $alternativeDomainNames): self
    {
        $this->alternativeDomainNames = $alternativeDomainNames;
        return $this;
    }

    public function getOriginPublicDNS(): ?array
    {
        return $this->originPublicDNS;
    }

    public function setOriginPublicDNS(?array $originPublicDNS): self
    {
        $this->originPublicDNS = $originPublicDNS;
        return $this;
    }

    public function getUpdateTime(): ?\DateTimeImmutable
    {
        return $this->updateTime;
    }

    public function setUpdateTime(?\DateTimeImmutable $updateTime): self
    {
        $this->updateTime = $updateTime;
        return $this;
    }
}
