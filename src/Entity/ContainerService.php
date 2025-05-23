<?php

namespace AwsLightsailBundle\Entity;

use AwsLightsailBundle\Enum\ContainerServicePowerEnum;
use AwsLightsailBundle\Enum\ContainerServiceStateEnum;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;

#[ORM\Entity]
#[ORM\Table(name: 'aws_lightsail_container_service', options: ['comment' => 'AWS Lightsail 容器服务表'])]
class ContainerService implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '容器服务名称'])]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => 'AWS ARN'])]
    private string $arn;

    #[ORM\Column(type: 'string', length: 50, enumType: ContainerServicePowerEnum::class, options: ['comment' => '容器服务计算能力'])]
    private ContainerServicePowerEnum $power;

    #[ORM\Column(type: 'integer', options: ['comment' => '缩放数量'])]
    private int $scale;

    #[ORM\Column(type: 'string', length: 50, enumType: ContainerServiceStateEnum::class, options: ['comment' => '服务状态'])]
    private ContainerServiceStateEnum $state;

    #[ORM\Column(type: 'string', length: 50, options: ['comment' => 'AWS 区域'])]
    private string $region;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['comment' => '服务 URL'])]
    private ?string $url = null;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => '当前部署配置'])]
    private ?array $currentDeployment = null;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => '下一次部署配置'])]
    private ?array $nextDeployment = null;

    #[ORM\Column(type: 'boolean', options: ['comment' => '是否启用公共域名'])]
    private bool $isPublicDomainEnabled = false;

    #[ORM\Column(type: 'boolean', options: ['comment' => '是否启用私有域名'])]
    private bool $isPrivateDomainEnabled = false;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => '私有域名'])]
    private ?array $privateDomainName = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['comment' => '公共域名'])]
    private ?string $publicDomainNames = null;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => '容器镜像'])]
    private ?array $containerImages = null;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => '标签'])]
    private ?array $tags = null;

    #[CreateTimeColumn]
    #[ORM\Column(type: 'datetime_immutable', options: ['comment' => '创建时间'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true, options: ['comment' => '同步时间'])]
    private ?\DateTimeImmutable $syncedAt = null;

    #[ORM\ManyToOne(targetEntity: AwsCredential::class)]
    #[ORM\JoinColumn(nullable: false)]
    private AwsCredential $credential;

    #[UpdateTimeColumn]
    #[ORM\Column(type: 'datetime_immutable', nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->state = ContainerServiceStateEnum::UNKNOWN;
        $this->power = ContainerServicePowerEnum::NANO;
        $this->scale = 1;
    }

    public function __toString(): string
    {
        return sprintf('ContainerService %s (%s) - %s, scale: %d', $this->name, $this->state->value, $this->power->value, $this->scale);
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

    public function getPower(): ContainerServicePowerEnum
    {
        return $this->power;
    }

    public function setPower(ContainerServicePowerEnum $power): self
    {
        $this->power = $power;
        return $this;
    }

    public function getScale(): int
    {
        return $this->scale;
    }

    public function setScale(int $scale): self
    {
        $this->scale = $scale;
        return $this;
    }

    public function getState(): ContainerServiceStateEnum
    {
        return $this->state;
    }

    public function setState(ContainerServiceStateEnum $state): self
    {
        $this->state = $state;
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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function getCurrentDeployment(): ?array
    {
        return $this->currentDeployment;
    }

    public function setCurrentDeployment(?array $currentDeployment): self
    {
        $this->currentDeployment = $currentDeployment;
        return $this;
    }

    public function getNextDeployment(): ?array
    {
        return $this->nextDeployment;
    }

    public function setNextDeployment(?array $nextDeployment): self
    {
        $this->nextDeployment = $nextDeployment;
        return $this;
    }

    public function isPublicDomainEnabled(): bool
    {
        return $this->isPublicDomainEnabled;
    }

    public function setIsPublicDomainEnabled(bool $isPublicDomainEnabled): self
    {
        $this->isPublicDomainEnabled = $isPublicDomainEnabled;
        return $this;
    }

    public function isPrivateDomainEnabled(): bool
    {
        return $this->isPrivateDomainEnabled;
    }

    public function setIsPrivateDomainEnabled(bool $isPrivateDomainEnabled): self
    {
        $this->isPrivateDomainEnabled = $isPrivateDomainEnabled;
        return $this;
    }

    public function getPrivateDomainName(): ?array
    {
        return $this->privateDomainName;
    }

    public function setPrivateDomainName(?array $privateDomainName): self
    {
        $this->privateDomainName = $privateDomainName;
        return $this;
    }

    public function getPublicDomainNames(): ?string
    {
        return $this->publicDomainNames;
    }

    public function setPublicDomainNames(?string $publicDomainNames): self
    {
        $this->publicDomainNames = $publicDomainNames;
        return $this;
    }

    public function getContainerImages(): ?array
    {
        return $this->containerImages;
    }

    public function setContainerImages(?array $containerImages): self
    {
        $this->containerImages = $containerImages;
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

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getSyncedAt(): ?\DateTimeImmutable
    {
        return $this->syncedAt;
    }

    public function setSyncedAt(?\DateTimeImmutable $syncedAt): self
    {
        $this->syncedAt = $syncedAt;
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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
} 