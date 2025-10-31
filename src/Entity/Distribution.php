<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Entity;

use AwsLightsailBundle\Enum\DistributionStatusEnum;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity]
#[ORM\Table(name: 'aws_lightsail_distribution', options: ['comment' => 'AWS Lightsail CDN 分发表'])]
class Distribution implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '主键ID'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '分发名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => 'AWS ARN'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $arn;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '默认域名'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $defaultDomainName;

    #[ORM\Column(type: Types::STRING, length: 50, enumType: DistributionStatusEnum::class, options: ['comment' => '状态'])]
    #[Assert\Choice(callback: [DistributionStatusEnum::class, 'cases'])]
    private DistributionStatusEnum $status;

    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => 'AWS 区域'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $region;

    /**
     * @var array<string, mixed>
     */
    #[ORM\Column(type: Types::JSON, options: ['comment' => '源站配置'])]
    #[Assert\Type(type: 'array')]
    private array $originConfigs = [];

    /**
     * @var array<string, mixed>
     */
    #[ORM\Column(type: Types::JSON, options: ['comment' => '默认缓存行为'])]
    #[Assert\Type(type: 'array')]
    private array $defaultCacheBehavior = [];

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '缓存行为'])]
    #[Assert\Type(type: 'array')]
    private ?array $cacheBehaviors = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否启用'])]
    #[Assert\Type(type: 'bool')]
    private bool $isEnabled = true;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '证书名称'])]
    #[Assert\Length(max: 255)]
    private ?string $certificateName = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '查看器协议策略'])]
    #[Assert\Type(type: 'bool')]
    private bool $viewerProtocolPolicy = false;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '标签'])]
    #[Assert\Type(type: 'array')]
    private ?array $tags = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '同步时间'])]
    #[Assert\Type(type: '\DateTimeImmutable')]
    private ?\DateTimeImmutable $syncTime = null;

    #[ORM\ManyToOne(targetEntity: AwsCredential::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private AwsCredential $credential;

    /**
     * @var array<int, string>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '替代域名'])]
    #[Assert\Type(type: 'array')]
    private ?array $alternativeDomainNames = null;

    /**
     * @var array<int, string>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '源站公共DNS'])]
    #[Assert\Type(type: 'array')]
    private ?array $originPublicDNS = null;

    public function __construct()
    {
        $this->status = DistributionStatusEnum::PENDING;
    }

    public function __toString(): string
    {
        return \sprintf('Distribution %s (%s)', $this->name, $this->status->value);
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

    public function getArn(): string
    {
        return $this->arn;
    }

    public function setArn(string $arn): void
    {
        $this->arn = $arn;
    }

    public function getDefaultDomainName(): string
    {
        return $this->defaultDomainName;
    }

    public function setDefaultDomainName(string $defaultDomainName): void
    {
        $this->defaultDomainName = $defaultDomainName;
    }

    public function getStatus(): DistributionStatusEnum
    {
        return $this->status;
    }

    public function setStatus(DistributionStatusEnum $status): void
    {
        $this->status = $status;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function setRegion(string $region): void
    {
        $this->region = $region;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOriginConfigs(): array
    {
        return $this->originConfigs;
    }

    /**
     * @param array<string, mixed> $originConfigs
     */
    public function setOriginConfigs(array $originConfigs): void
    {
        $this->originConfigs = $originConfigs;
    }

    /**
     * @return array<string, mixed>
     */
    public function getDefaultCacheBehavior(): array
    {
        return $this->defaultCacheBehavior;
    }

    /**
     * @param array<string, mixed> $defaultCacheBehavior
     */
    public function setDefaultCacheBehavior(array $defaultCacheBehavior): void
    {
        $this->defaultCacheBehavior = $defaultCacheBehavior;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getCacheBehaviors(): ?array
    {
        return $this->cacheBehaviors;
    }

    /**
     * @param array<string, mixed>|null $cacheBehaviors
     */
    public function setCacheBehaviors(?array $cacheBehaviors): void
    {
        $this->cacheBehaviors = $cacheBehaviors;
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): void
    {
        $this->isEnabled = $isEnabled;
    }

    public function getCertificateName(): ?string
    {
        return $this->certificateName;
    }

    public function setCertificateName(?string $certificateName): void
    {
        $this->certificateName = $certificateName;
    }

    public function getViewerProtocolPolicy(): bool
    {
        return $this->viewerProtocolPolicy;
    }

    public function setViewerProtocolPolicy(bool $viewerProtocolPolicy): void
    {
        $this->viewerProtocolPolicy = $viewerProtocolPolicy;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getTags(): ?array
    {
        return $this->tags;
    }

    /**
     * @param array<string, mixed>|null $tags
     */
    public function setTags(?array $tags): void
    {
        $this->tags = $tags;
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

    public function getCredential(): AwsCredential
    {
        return $this->credential;
    }

    public function setCredential(AwsCredential $credential): void
    {
        $this->credential = $credential;
    }

    /**
     * @return array<int, string>|null
     */
    public function getAlternativeDomainNames(): ?array
    {
        return $this->alternativeDomainNames;
    }

    /**
     * @param array<int, string>|null $alternativeDomainNames
     */
    public function setAlternativeDomainNames(?array $alternativeDomainNames): void
    {
        $this->alternativeDomainNames = $alternativeDomainNames;
    }

    /**
     * @return array<int, string>|null
     */
    public function getOriginPublicDNS(): ?array
    {
        return $this->originPublicDNS;
    }

    /**
     * @param array<int, string>|null $originPublicDNS
     */
    public function setOriginPublicDNS(?array $originPublicDNS): void
    {
        $this->originPublicDNS = $originPublicDNS;
    }
}
