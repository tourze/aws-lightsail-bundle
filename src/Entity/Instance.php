<?php

namespace AwsLightsailBundle\Entity;

use AwsLightsailBundle\Enum\InstanceBlueprintEnum;
use AwsLightsailBundle\Enum\InstanceBundleEnum;
use AwsLightsailBundle\Enum\InstanceStateEnum;
use AwsLightsailBundle\Repository\InstanceRepository;
use Carbon\Carbon;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;

#[ORM\Entity(repositoryClass: InstanceRepository::class)]
#[ORM\Table(name: 'aws_lightsail_instance', options: ['comment' => 'AWS Lightsail 实例表'])]
class Instance implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '实例名称'])]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => 'AWS ARN'])]
    private string $arn;

    #[ORM\Column(type: 'string', length: 100, enumType: InstanceStateEnum::class, options: ['comment' => '实例状态'])]
    private InstanceStateEnum $state = InstanceStateEnum::UNKNOWN;

    #[ORM\Column(type: 'integer', nullable: true, options: ['comment' => '实例状态代码'])]
    private ?int $stateCode = null;

    #[ORM\Column(type: 'string', length: 100, enumType: InstanceBlueprintEnum::class, options: ['comment' => '蓝图类型'])]
    private InstanceBlueprintEnum $blueprint;

    #[ORM\Column(type: 'string', length: 100, nullable: true, options: ['comment' => '蓝图名称'])]
    private ?string $blueprintName = null;

    #[ORM\Column(type: 'string', length: 100, enumType: InstanceBundleEnum::class, options: ['comment' => '实例套餐'])]
    private InstanceBundleEnum $bundle;

    #[ORM\Column(type: 'string', length: 50, options: ['comment' => 'AWS 区域'])]
    private string $region;

    #[ORM\Column(type: 'string', length: 50, nullable: true, options: ['comment' => '可用区'])]
    private ?string $availabilityZone = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true, options: ['comment' => '资源类型'])]
    private ?string $resourceType = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true, options: ['comment' => '公网 IP 地址'])]
    private ?string $publicIpAddress = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true, options: ['comment' => '私网 IP 地址'])]
    private ?string $privateIpAddress = null;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => 'IPv6 地址列表'])]
    private ?array $ipv6Addresses = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true, options: ['comment' => 'IP 地址类型（ipv4/ipv6/dualstack）'])]
    private ?string $ipAddressType = null;

    #[ORM\Column(type: 'boolean', options: ['comment' => '是否为静态 IP'])]
    private bool $isStaticIp = false;

    #[ORM\ManyToOne(targetEntity: KeyPair::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?KeyPair $keyPair = null;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => '标签'])]
    private ?array $tags = null;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => '硬件配置'])]
    private ?array $hardware = null;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => '网络配置'])]
    private ?array $networking = null;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => '元数据选项'])]
    private ?array $metadataOptions = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => 'AWS 创建时间'])]
    private ?\DateTimeInterface $awsCreatedAt = null;

    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['comment' => '创建时间'])]
    private \DateTimeInterface $createdAt;

    #[ORM\ManyToOne(targetEntity: AwsCredential::class)]
    #[ORM\JoinColumn(nullable: false)]
    private AwsCredential $credential;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '同步时间'])]
    private ?\DateTimeInterface $syncedAt = null;

    #[ORM\Column(type: 'text', nullable: true, options: ['comment' => '用户名'])]
    private ?string $username = null;

    #[ORM\Column(type: 'boolean', options: ['comment' => '是否启用监控'])]
    private bool $isMonitoring = false;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['comment' => '支持代码'])]
    private ?string $supportCode = null;

    #[UpdateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = Carbon::now();
    }

    public function __toString(): string
    {
        return sprintf('Instance %s (%s, %s)', $this->name, $this->state->value, $this->region);
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

    public function getState(): InstanceStateEnum
    {
        return $this->state;
    }

    public function setState(InstanceStateEnum $state): self
    {
        $this->state = $state;
        return $this;
    }

    public function getStateCode(): ?int
    {
        return $this->stateCode;
    }

    public function setStateCode(?int $stateCode): self
    {
        $this->stateCode = $stateCode;
        return $this;
    }

    public function getBlueprint(): InstanceBlueprintEnum
    {
        return $this->blueprint;
    }

    public function setBlueprint(InstanceBlueprintEnum $blueprint): self
    {
        $this->blueprint = $blueprint;
        return $this;
    }

    public function getBlueprintName(): ?string
    {
        return $this->blueprintName;
    }

    public function setBlueprintName(?string $blueprintName): self
    {
        $this->blueprintName = $blueprintName;
        return $this;
    }

    public function getBundle(): InstanceBundleEnum
    {
        return $this->bundle;
    }

    public function setBundle(InstanceBundleEnum $bundle): self
    {
        $this->bundle = $bundle;
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

    public function getAvailabilityZone(): ?string
    {
        return $this->availabilityZone;
    }

    public function setAvailabilityZone(?string $availabilityZone): self
    {
        $this->availabilityZone = $availabilityZone;
        return $this;
    }

    public function getResourceType(): ?string
    {
        return $this->resourceType;
    }

    public function setResourceType(?string $resourceType): self
    {
        $this->resourceType = $resourceType;
        return $this;
    }

    public function getPublicIpAddress(): ?string
    {
        return $this->publicIpAddress;
    }

    public function setPublicIpAddress(?string $publicIpAddress): self
    {
        $this->publicIpAddress = $publicIpAddress;
        return $this;
    }

    public function getPrivateIpAddress(): ?string
    {
        return $this->privateIpAddress;
    }

    public function setPrivateIpAddress(?string $privateIpAddress): self
    {
        $this->privateIpAddress = $privateIpAddress;
        return $this;
    }

    public function getIpv6Addresses(): ?array
    {
        return $this->ipv6Addresses;
    }

    public function setIpv6Addresses(?array $ipv6Addresses): self
    {
        $this->ipv6Addresses = $ipv6Addresses;
        return $this;
    }

    public function getIpAddressType(): ?string
    {
        return $this->ipAddressType;
    }

    public function setIpAddressType(?string $ipAddressType): self
    {
        $this->ipAddressType = $ipAddressType;
        return $this;
    }

    public function isStaticIp(): bool
    {
        return $this->isStaticIp;
    }

    public function setIsStaticIp(bool $isStaticIp): self
    {
        $this->isStaticIp = $isStaticIp;
        return $this;
    }

    public function getKeyPair(): ?KeyPair
    {
        return $this->keyPair;
    }

    public function setKeyPair(?KeyPair $keyPair): self
    {
        $this->keyPair = $keyPair;
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

    public function getHardware(): ?array
    {
        return $this->hardware;
    }

    public function setHardware(?array $hardware): self
    {
        $this->hardware = $hardware;
        return $this;
    }

    public function getNetworking(): ?array
    {
        return $this->networking;
    }

    public function setNetworking(?array $networking): self
    {
        $this->networking = $networking;
        return $this;
    }

    public function getMetadataOptions(): ?array
    {
        return $this->metadataOptions;
    }

    public function setMetadataOptions(?array $metadataOptions): self
    {
        $this->metadataOptions = $metadataOptions;
        return $this;
    }

    public function getAwsCreatedAt(): ?\DateTimeInterface
    {
        return $this->awsCreatedAt;
    }

    public function setAwsCreatedAt(?\DateTimeInterface $awsCreatedAt): self
    {
        $this->awsCreatedAt = $awsCreatedAt;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
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

    public function getSyncedAt(): ?\DateTimeInterface
    {
        return $this->syncedAt;
    }

    public function setSyncedAt(?\DateTimeInterface $syncedAt): self
    {
        $this->syncedAt = $syncedAt;
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function isMonitoring(): bool
    {
        return $this->isMonitoring;
    }

    public function setIsMonitoring(bool $isMonitoring): self
    {
        $this->isMonitoring = $isMonitoring;
        return $this;
    }

    public function getSupportCode(): ?string
    {
        return $this->supportCode;
    }

    public function setSupportCode(?string $supportCode): self
    {
        $this->supportCode = $supportCode;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
