<?php

namespace AwsLightsailBundle\Entity;

use AwsLightsailBundle\Enum\DiskStateEnum;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;

#[ORM\Entity]
#[ORM\Table(name: 'aws_lightsail_disk', options: ['comment' => 'AWS Lightsail 磁盘表'])]
class Disk implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '磁盘名称'])]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => 'AWS ARN'])]
    private string $arn;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['comment' => '挂载到的实例'])]
    private ?string $attachedTo = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['comment' => '挂载状态'])]
    private ?string $attachmentState = null;

    #[ORM\Column(type: 'boolean', options: ['comment' => '是否为系统磁盘'])]
    private bool $isSystemDisk = false;

    #[ORM\Column(type: 'string', length: 50, enumType: DiskStateEnum::class, options: ['comment' => '磁盘状态'])]
    private DiskStateEnum $state;

    #[ORM\Column(type: 'string', length: 50, options: ['comment' => 'AWS 区域'])]
    private string $region;

    #[ORM\Column(type: 'bigint', options: ['comment' => '大小(GB)'])]
    private int $sizeInGb;

    #[ORM\Column(type: 'integer', nullable: true, options: ['comment' => 'IOPS'])]
    private ?int $iops = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['comment' => '路径'])]
    private ?string $path = null;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => '标签'])]
    private ?array $tags = null;

    #[ORM\Column(type: 'boolean', options: ['comment' => '是否配置自动快照'])]
    private bool $isAutoSnapshotConfigured = false;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['comment' => '支持代码'])]
    private ?string $supportCode = null;

    #[CreateTimeColumn]
    #[ORM\Column(type: 'datetime_immutable', options: ['comment' => '创建时间'])]
    private \DateTimeImmutable $createTime;

    #[ORM\Column(type: 'datetime_immutable', nullable: true, options: ['comment' => '同步时间'])]
    private ?\DateTimeImmutable $syncTime = null;

    #[ORM\ManyToOne(targetEntity: AwsCredential::class)]
    #[ORM\JoinColumn(nullable: false)]
    private AwsCredential $credential;
    
    #[UpdateTimeColumn]
    #[ORM\Column(type: 'datetime_immutable', nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeImmutable $updateTime = null;

    public function __construct()
    {
        $this->createTime = new \DateTimeImmutable();
        $this->state = DiskStateEnum::UNKNOWN;
    }

    public function __toString(): string
    {
        return sprintf('Disk %s (%s, %d GB)', $this->name, $this->state->value, $this->sizeInGb);
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

    public function getAttachedTo(): ?string
    {
        return $this->attachedTo;
    }

    public function setAttachedTo(?string $attachedTo): self
    {
        $this->attachedTo = $attachedTo;
        return $this;
    }

    public function getAttachmentState(): ?string
    {
        return $this->attachmentState;
    }

    public function setAttachmentState(?string $attachmentState): self
    {
        $this->attachmentState = $attachmentState;
        return $this;
    }

    public function isSystemDisk(): bool
    {
        return $this->isSystemDisk;
    }

    public function setIsSystemDisk(bool $isSystemDisk): self
    {
        $this->isSystemDisk = $isSystemDisk;
        return $this;
    }

    public function getState(): DiskStateEnum
    {
        return $this->state;
    }

    public function setState(DiskStateEnum $state): self
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

    public function getSizeInGb(): int
    {
        return $this->sizeInGb;
    }

    public function setSizeInGb(int $sizeInGb): self
    {
        $this->sizeInGb = $sizeInGb;
        return $this;
    }

    public function getIops(): ?int
    {
        return $this->iops;
    }

    public function setIops(?int $iops): self
    {
        $this->iops = $iops;
        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): self
    {
        $this->path = $path;
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

    public function isAutoSnapshotConfigured(): bool
    {
        return $this->isAutoSnapshotConfigured;
    }

    public function setIsAutoSnapshotConfigured(bool $isAutoSnapshotConfigured): self
    {
        $this->isAutoSnapshotConfigured = $isAutoSnapshotConfigured;
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
