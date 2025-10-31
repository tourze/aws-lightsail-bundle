<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Entity;

use AwsLightsailBundle\Enum\InstanceBlueprintEnum;
use AwsLightsailBundle\Enum\InstanceBundleEnum;
use AwsLightsailBundle\Enum\InstanceStateEnum;
use AwsLightsailBundle\Repository\InstanceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: InstanceRepository::class)]
#[ORM\Table(name: 'aws_lightsail_instance', options: ['comment' => 'AWS Lightsail 实例表'])]
class Instance implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '主键ID'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '实例名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => 'AWS ARN'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $arn;

    #[ORM\Column(type: Types::STRING, length: 100, enumType: InstanceStateEnum::class, options: ['comment' => '实例状态'])]
    #[Assert\Choice(callback: [InstanceStateEnum::class, 'cases'])]
    #[Assert\NotBlank]
    private InstanceStateEnum $state = InstanceStateEnum::UNKNOWN;

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '实例状态代码'])]
    #[Assert\Type(type: 'int')]
    private ?int $stateCode = null;

    #[ORM\Column(type: Types::STRING, length: 100, enumType: InstanceBlueprintEnum::class, options: ['comment' => '蓝图类型'])]
    #[Assert\Choice(callback: [InstanceBlueprintEnum::class, 'cases'])]
    #[Assert\NotBlank]
    private InstanceBlueprintEnum $blueprint;

    #[ORM\Column(type: Types::STRING, length: 100, nullable: true, options: ['comment' => '蓝图名称'])]
    #[Assert\Length(max: 100)]
    private ?string $blueprintName = null;

    #[ORM\Column(type: Types::STRING, length: 100, enumType: InstanceBundleEnum::class, options: ['comment' => '实例套餐'])]
    #[Assert\Choice(callback: [InstanceBundleEnum::class, 'cases'])]
    #[Assert\NotBlank]
    private InstanceBundleEnum $bundle;

    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => 'AWS 区域'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $region;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true, options: ['comment' => '可用区'])]
    #[Assert\Length(max: 50)]
    private ?string $availabilityZone = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true, options: ['comment' => '资源类型'])]
    #[Assert\Length(max: 50)]
    private ?string $resourceType = null;

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true, options: ['comment' => '公网 IP 地址'])]
    #[Assert\Length(max: 20)]
    #[Assert\Ip]
    private ?string $publicIpAddress = null;

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true, options: ['comment' => '私网 IP 地址'])]
    #[Assert\Length(max: 20)]
    #[Assert\Ip]
    private ?string $privateIpAddress = null;

    /**
     * @var array<int, string>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => 'IPv6 地址列表'])]
    #[Assert\Type(type: 'array')]
    private ?array $ipv6Addresses = null;

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true, options: ['comment' => 'IP 地址类型（ipv4/ipv6/dualstack）'])]
    #[Assert\Length(max: 20)]
    private ?string $ipAddressType = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否为静态 IP'])]
    #[Assert\Type(type: 'bool')]
    private bool $isStaticIp = false;

    #[ORM\ManyToOne(targetEntity: KeyPair::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?KeyPair $keyPair = null;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '标签'])]
    #[Assert\Type(type: 'array')]
    private ?array $tags = null;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '硬件配置'])]
    #[Assert\Type(type: 'array')]
    private ?array $hardware = null;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '网络配置'])]
    #[Assert\Type(type: 'array')]
    private ?array $networking = null;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '元数据选项'])]
    #[Assert\Type(type: 'array')]
    private ?array $metadataOptions = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => 'AWS 创建时间'])]
    #[Assert\Type(type: '\DateTimeInterface')]
    private ?\DateTimeImmutable $awsCreationTime = null;

    #[ORM\ManyToOne(targetEntity: AwsCredential::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private AwsCredential $credential;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '同步时间'])]
    #[Assert\Type(type: '\DateTimeInterface')]
    private ?\DateTimeImmutable $syncTime = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '用户名'])]
    #[Assert\Length(max: 65535)]
    private ?string $username = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否启用监控'])]
    #[Assert\Type(type: 'bool')]
    private bool $isMonitoring = false;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '支持代码'])]
    #[Assert\Length(max: 255)]
    private ?string $supportCode = null;

    public function __construct()
    {
        // createTime 会通过 TimestampableAware trait 自动初始化
    }

    public function __toString(): string
    {
        return \sprintf('Instance %s (%s, %s)', $this->name, $this->state->value, $this->region);
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

    public function getState(): InstanceStateEnum
    {
        return $this->state;
    }

    public function setState(InstanceStateEnum $state): void
    {
        $this->state = $state;
    }

    public function getStateCode(): ?int
    {
        return $this->stateCode;
    }

    public function setStateCode(?int $stateCode): void
    {
        $this->stateCode = $stateCode;
    }

    public function getBlueprint(): InstanceBlueprintEnum
    {
        return $this->blueprint;
    }

    public function setBlueprint(InstanceBlueprintEnum $blueprint): void
    {
        $this->blueprint = $blueprint;
    }

    public function getBlueprintName(): ?string
    {
        return $this->blueprintName;
    }

    public function setBlueprintName(?string $blueprintName): void
    {
        $this->blueprintName = $blueprintName;
    }

    public function getBundle(): InstanceBundleEnum
    {
        return $this->bundle;
    }

    public function setBundle(InstanceBundleEnum $bundle): void
    {
        $this->bundle = $bundle;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function setRegion(string $region): void
    {
        $this->region = $region;
    }

    public function getAvailabilityZone(): ?string
    {
        return $this->availabilityZone;
    }

    public function setAvailabilityZone(?string $availabilityZone): void
    {
        $this->availabilityZone = $availabilityZone;
    }

    public function getResourceType(): ?string
    {
        return $this->resourceType;
    }

    public function setResourceType(?string $resourceType): void
    {
        $this->resourceType = $resourceType;
    }

    public function getPublicIpAddress(): ?string
    {
        return $this->publicIpAddress;
    }

    public function setPublicIpAddress(?string $publicIpAddress): void
    {
        $this->publicIpAddress = $publicIpAddress;
    }

    public function getPrivateIpAddress(): ?string
    {
        return $this->privateIpAddress;
    }

    public function setPrivateIpAddress(?string $privateIpAddress): void
    {
        $this->privateIpAddress = $privateIpAddress;
    }

    /**
     * @return array<int, string>|null
     */
    public function getIpv6Addresses(): ?array
    {
        return $this->ipv6Addresses;
    }

    /**
     * @param array<int, string>|null $ipv6Addresses
     */
    public function setIpv6Addresses(?array $ipv6Addresses): void
    {
        $this->ipv6Addresses = $ipv6Addresses;
    }

    public function getIpAddressType(): ?string
    {
        return $this->ipAddressType;
    }

    public function setIpAddressType(?string $ipAddressType): void
    {
        $this->ipAddressType = $ipAddressType;
    }

    public function isStaticIp(): bool
    {
        return $this->isStaticIp;
    }

    public function setIsStaticIp(bool $isStaticIp): void
    {
        $this->isStaticIp = $isStaticIp;
    }

    public function getKeyPair(): ?KeyPair
    {
        return $this->keyPair;
    }

    public function setKeyPair(?KeyPair $keyPair): void
    {
        $this->keyPair = $keyPair;
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

    /**
     * @return array<string, mixed>|null
     */
    public function getHardware(): ?array
    {
        return $this->hardware;
    }

    /**
     * @param array<string, mixed>|null $hardware
     */
    public function setHardware(?array $hardware): void
    {
        $this->hardware = $hardware;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getNetworking(): ?array
    {
        return $this->networking;
    }

    /**
     * @param array<string, mixed>|null $networking
     */
    public function setNetworking(?array $networking): void
    {
        $this->networking = $networking;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getMetadataOptions(): ?array
    {
        return $this->metadataOptions;
    }

    /**
     * @param array<string, mixed>|null $metadataOptions
     */
    public function setMetadataOptions(?array $metadataOptions): void
    {
        $this->metadataOptions = $metadataOptions;
    }

    public function getAwsCreationTime(): ?\DateTimeImmutable
    {
        return $this->awsCreationTime;
    }

    public function setAwsCreationTime(?\DateTimeInterface $awsCreationTime): void
    {
        if (null !== $awsCreationTime && !$awsCreationTime instanceof \DateTimeImmutable) {
            $awsCreationTime = \DateTimeImmutable::createFromInterface($awsCreationTime);
        }
        $this->awsCreationTime = $awsCreationTime;
    }

    public function getCredential(): AwsCredential
    {
        return $this->credential;
    }

    public function setCredential(AwsCredential $credential): void
    {
        $this->credential = $credential;
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

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    public function isMonitoring(): bool
    {
        return $this->isMonitoring;
    }

    public function setIsMonitoring(bool $isMonitoring): void
    {
        $this->isMonitoring = $isMonitoring;
    }

    public function getSupportCode(): ?string
    {
        return $this->supportCode;
    }

    public function setSupportCode(?string $supportCode): void
    {
        $this->supportCode = $supportCode;
    }
}
