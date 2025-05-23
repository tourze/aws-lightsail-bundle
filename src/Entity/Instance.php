<?php

namespace AwsLightsailBundle\Entity;

use AwsLightsailBundle\Enum\InstanceBlueprintEnum;
use AwsLightsailBundle\Enum\InstanceBundleEnum;
use AwsLightsailBundle\Enum\InstanceStateEnum;
use AwsLightsailBundle\Repository\InstanceRepository;
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
    private InstanceStateEnum $state;

    #[ORM\Column(type: 'string', length: 100, enumType: InstanceBlueprintEnum::class, options: ['comment' => '蓝图类型'])]
    private InstanceBlueprintEnum $blueprint;

    #[ORM\Column(type: 'string', length: 100, enumType: InstanceBundleEnum::class, options: ['comment' => '实例套餐'])]
    private InstanceBundleEnum $bundle;

    #[ORM\Column(type: 'string', length: 50, options: ['comment' => 'AWS 区域'])]
    private string $region;

    #[ORM\Column(type: 'string', length: 20, nullable: true, options: ['comment' => '公网 IP 地址'])]
    private ?string $publicIpAddress = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true, options: ['comment' => '私网 IP 地址'])]
    private ?string $privateIpAddress = null;

    #[ORM\Column(type: 'text', nullable: true, options: ['comment' => '密钥对名称'])]
    private ?string $keyPairName = null;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => '标签'])]
    private ?array $tags = null;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => '硬件配置'])]
    private ?array $hardware = null;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => '网络配置'])]
    private ?array $networking = null;

    #[CreateTimeColumn]
    #[ORM\Column(type: 'datetime_immutable', options: ['comment' => '创建时间'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(targetEntity: AwsCredential::class)]
    #[ORM\JoinColumn(nullable: false)]
    private AwsCredential $credential;

    #[ORM\Column(type: 'datetime_immutable', nullable: true, options: ['comment' => '同步时间'])]
    private ?\DateTimeImmutable $syncedAt = null;

    #[ORM\Column(type: 'text', nullable: true, options: ['comment' => '用户名'])]
    private ?string $username = null;

    #[ORM\Column(type: 'boolean', options: ['comment' => '是否启用监控'])]
    private bool $isMonitoring = false;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['comment' => '支持代码'])]
    private ?string $supportCode = null;

    #[UpdateTimeColumn]
    #[ORM\Column(type: 'datetime_immutable', nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->state = InstanceStateEnum::UNKNOWN;
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

    public function getBlueprint(): InstanceBlueprintEnum
    {
        return $this->blueprint;
    }

    public function setBlueprint(InstanceBlueprintEnum $blueprint): self
    {
        $this->blueprint = $blueprint;
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

    public function getKeyPairName(): ?string
    {
        return $this->keyPairName;
    }

    public function setKeyPairName(?string $keyPairName): self
    {
        $this->keyPairName = $keyPairName;
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

    public function getCreatedAt(): \DateTimeImmutable
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

    public function getSyncedAt(): ?\DateTimeImmutable
    {
        return $this->syncedAt;
    }

    public function setSyncedAt(?\DateTimeImmutable $syncedAt): self
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
