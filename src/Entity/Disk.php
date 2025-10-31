<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Entity;

use AwsLightsailBundle\Enum\DiskStateEnum;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity]
#[ORM\Table(name: 'aws_lightsail_disk', options: ['comment' => 'AWS Lightsail 磁盘表'])]
class Disk implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '主键ID'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '磁盘名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => 'AWS ARN'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $arn;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '挂载到的实例'])]
    #[Assert\Length(max: 255)]
    private ?string $attachedTo = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '挂载状态'])]
    #[Assert\Length(max: 255)]
    private ?string $attachmentState = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否为系统磁盘'])]
    #[Assert\Type(type: 'bool')]
    private bool $isSystemDisk = false;

    #[ORM\Column(type: Types::STRING, length: 50, enumType: DiskStateEnum::class, options: ['comment' => '磁盘状态'])]
    #[Assert\Choice(callback: [DiskStateEnum::class, 'cases'])]
    private DiskStateEnum $state;

    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => 'AWS 区域'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $region;

    #[ORM\Column(type: Types::BIGINT, options: ['comment' => '大小(GB)'])]
    #[Assert\Positive]
    private int $sizeInGb;

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => 'IOPS'])]
    #[Assert\PositiveOrZero]
    private ?int $iops = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '路径'])]
    #[Assert\Length(max: 255)]
    private ?string $path = null;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '标签'])]
    #[Assert\Type(type: 'array')]
    private ?array $tags = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否配置自动快照'])]
    #[Assert\Type(type: 'bool')]
    private bool $isAutoSnapshotConfigured = false;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '支持代码'])]
    #[Assert\Length(max: 255)]
    private ?string $supportCode = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '同步时间'])]
    #[Assert\DateTime]
    private ?\DateTimeImmutable $syncTime = null;

    #[ORM\ManyToOne(targetEntity: AwsCredential::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private AwsCredential $credential;

    public function __construct()
    {
        $this->state = DiskStateEnum::UNKNOWN;
    }

    public function __toString(): string
    {
        return \sprintf('Disk %s (%s, %d GB)', $this->name, $this->state->value, $this->sizeInGb);
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

    public function getAttachedTo(): ?string
    {
        return $this->attachedTo;
    }

    public function setAttachedTo(?string $attachedTo): void
    {
        $this->attachedTo = $attachedTo;
    }

    public function getAttachmentState(): ?string
    {
        return $this->attachmentState;
    }

    public function setAttachmentState(?string $attachmentState): void
    {
        $this->attachmentState = $attachmentState;
    }

    public function isSystemDisk(): bool
    {
        return $this->isSystemDisk;
    }

    public function setIsSystemDisk(bool $isSystemDisk): void
    {
        $this->isSystemDisk = $isSystemDisk;
    }

    public function getState(): DiskStateEnum
    {
        return $this->state;
    }

    public function setState(DiskStateEnum $state): void
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

    public function getSizeInGb(): int
    {
        return $this->sizeInGb;
    }

    public function setSizeInGb(int $sizeInGb): void
    {
        $this->sizeInGb = $sizeInGb;
    }

    public function getIops(): ?int
    {
        return $this->iops;
    }

    public function setIops(?int $iops): void
    {
        $this->iops = $iops;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): void
    {
        $this->path = $path;
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

    public function isAutoSnapshotConfigured(): bool
    {
        return $this->isAutoSnapshotConfigured;
    }

    public function setIsAutoSnapshotConfigured(bool $isAutoSnapshotConfigured): void
    {
        $this->isAutoSnapshotConfigured = $isAutoSnapshotConfigured;
    }

    public function getSupportCode(): ?string
    {
        return $this->supportCode;
    }

    public function setSupportCode(?string $supportCode): void
    {
        $this->supportCode = $supportCode;
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
