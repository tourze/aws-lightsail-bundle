<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Entity;

use AwsLightsailBundle\Enum\ContainerServicePowerEnum;
use AwsLightsailBundle\Enum\ContainerServiceStateEnum;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity]
#[ORM\Table(name: 'aws_lightsail_container_service', options: ['comment' => 'AWS Lightsail 容器服务表'])]
class ContainerService implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '主键ID'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '容器服务名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => 'AWS ARN'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $arn;

    #[ORM\Column(type: Types::STRING, length: 50, enumType: ContainerServicePowerEnum::class, options: ['comment' => '容器服务计算能力'])]
    #[Assert\Choice(callback: [ContainerServicePowerEnum::class, 'cases'])]
    #[Assert\NotBlank]
    private ContainerServicePowerEnum $power;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '缩放数量'])]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    private int $scale;

    #[ORM\Column(type: Types::STRING, length: 50, enumType: ContainerServiceStateEnum::class, options: ['comment' => '服务状态'])]
    #[Assert\Choice(callback: [ContainerServiceStateEnum::class, 'cases'])]
    #[Assert\NotBlank]
    private ContainerServiceStateEnum $state;

    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => 'AWS 区域'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $region;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '服务 URL'])]
    #[Assert\Length(max: 255)]
    #[Assert\Url]
    private ?string $url = null;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '当前部署配置'])]
    #[Assert\Type(type: 'array')]
    private ?array $currentDeployment = null;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '下一次部署配置'])]
    #[Assert\Type(type: 'array')]
    private ?array $nextDeployment = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否启用公共域名'])]
    #[Assert\Type(type: 'bool')]
    private bool $isPublicDomainEnabled = false;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否启用私有域名'])]
    #[Assert\Type(type: 'bool')]
    private bool $isPrivateDomainEnabled = false;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '私有域名'])]
    #[Assert\Type(type: 'array')]
    private ?array $privateDomainName = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '公共域名'])]
    #[Assert\Length(max: 255)]
    private ?string $publicDomainNames = null;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '容器镜像'])]
    #[Assert\Type(type: 'array')]
    private ?array $containerImages = null;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '标签'])]
    #[Assert\Type(type: 'array')]
    private ?array $tags = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '同步时间'])]
    #[Assert\Type(type: '\DateTimeInterface')]
    private ?\DateTimeImmutable $syncTime = null;

    #[ORM\ManyToOne(targetEntity: AwsCredential::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private AwsCredential $credential;

    public function __construct()
    {
        // createTime 会通过 TimestampableAware trait 自动初始化
        $this->state = ContainerServiceStateEnum::UNKNOWN;
        $this->power = ContainerServicePowerEnum::NANO;
        $this->scale = 1;
    }

    public function __toString(): string
    {
        return \sprintf('ContainerService %s (%s) - %s, scale: %d', $this->name, $this->state->value, $this->power->value, $this->scale);
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

    public function getPower(): ContainerServicePowerEnum
    {
        return $this->power;
    }

    public function setPower(ContainerServicePowerEnum $power): void
    {
        $this->power = $power;
    }

    public function getScale(): int
    {
        return $this->scale;
    }

    public function setScale(int $scale): void
    {
        $this->scale = $scale;
    }

    public function getState(): ContainerServiceStateEnum
    {
        return $this->state;
    }

    public function setState(ContainerServiceStateEnum $state): void
    {
        $this->state = $state;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function setRegion(string $region): void
    {
        $this->region = $region;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getCurrentDeployment(): ?array
    {
        return $this->currentDeployment;
    }

    /**
     * @param array<string, mixed>|null $currentDeployment
     */
    public function setCurrentDeployment(?array $currentDeployment): void
    {
        $this->currentDeployment = $currentDeployment;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getNextDeployment(): ?array
    {
        return $this->nextDeployment;
    }

    /**
     * @param array<string, mixed>|null $nextDeployment
     */
    public function setNextDeployment(?array $nextDeployment): void
    {
        $this->nextDeployment = $nextDeployment;
    }

    public function isPublicDomainEnabled(): bool
    {
        return $this->isPublicDomainEnabled;
    }

    public function setIsPublicDomainEnabled(bool $isPublicDomainEnabled): void
    {
        $this->isPublicDomainEnabled = $isPublicDomainEnabled;
    }

    public function isPrivateDomainEnabled(): bool
    {
        return $this->isPrivateDomainEnabled;
    }

    public function setIsPrivateDomainEnabled(bool $isPrivateDomainEnabled): void
    {
        $this->isPrivateDomainEnabled = $isPrivateDomainEnabled;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getPrivateDomainName(): ?array
    {
        return $this->privateDomainName;
    }

    /**
     * @param array<string, mixed>|null $privateDomainName
     */
    public function setPrivateDomainName(?array $privateDomainName): void
    {
        $this->privateDomainName = $privateDomainName;
    }

    public function getPublicDomainNames(): ?string
    {
        return $this->publicDomainNames;
    }

    public function setPublicDomainNames(?string $publicDomainNames): void
    {
        $this->publicDomainNames = $publicDomainNames;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getContainerImages(): ?array
    {
        return $this->containerImages;
    }

    /**
     * @param array<string, mixed>|null $containerImages
     */
    public function setContainerImages(?array $containerImages): void
    {
        $this->containerImages = $containerImages;
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
}
