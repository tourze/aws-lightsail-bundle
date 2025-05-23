<?php

namespace AwsLightsailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;

#[ORM\Entity]
#[ORM\Table(name: 'aws_lightsail_disk_snapshot', options: ['comment' => 'AWS Lightsail 磁盘快照表'])]
class DiskSnapshot implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '快照名称'])]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => 'AWS ARN'])]
    private string $arn;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '磁盘名称'])]
    private string $diskName;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['comment' => '磁盘路径'])]
    private ?string $diskPath = null;

    #[ORM\Column(type: 'string', length: 50, options: ['comment' => 'AWS 区域'])]
    private string $region;

    #[ORM\Column(type: 'bigint', options: ['comment' => '大小(GB)'])]
    private int $sizeInGb;

    #[ORM\Column(type: 'text', nullable: true, options: ['comment' => '状态'])]
    private ?string $state = null;

    #[ORM\Column(type: 'text', nullable: true, options: ['comment' => '进度'])]
    private ?string $progress = null;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => '标签'])]
    private ?array $tags = null;

    #[ORM\Column(type: 'boolean', options: ['comment' => '是否来自自动快照'])]
    private bool $isFromAutoSnapshot = false;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['comment' => '来源磁盘快照名称'])]
    private ?string $fromDiskSnapshotName = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true, options: ['comment' => '来源区域'])]
    private ?string $fromRegion = null;

    #[CreateTimeColumn]
    #[ORM\Column(type: 'datetime_immutable', options: ['comment' => '创建时间'])]
    private \DateTimeImmutable $createTime;

    #[ORM\Column(type: 'datetime_immutable', nullable: true, options: ['comment' => '同步时间'])]
    private ?\DateTimeImmutable $syncTime = null;

    #[ORM\ManyToOne(targetEntity: AwsCredential::class)]
    #[ORM\JoinColumn(nullable: false)]
    private AwsCredential $credential;

    #[ORM\ManyToOne(targetEntity: Disk::class)]
    private ?Disk $disk = null;

    #[UpdateTimeColumn]
    #[ORM\Column(type: 'datetime_immutable', nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeImmutable $updateTime = null;

    public function __construct()
    {
        $this->createTime = new \DateTimeImmutable();
    }

    public function __toString(): string
    {
        return sprintf('DiskSnapshot %s (%s, %d GB)', $this->name, $this->diskName, $this->sizeInGb);
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

    public function getDiskName(): string
    {
        return $this->diskName;
    }

    public function setDiskName(string $diskName): self
    {
        $this->diskName = $diskName;
        return $this;
    }

    public function getDiskPath(): ?string
    {
        return $this->diskPath;
    }

    public function setDiskPath(?string $diskPath): self
    {
        $this->diskPath = $diskPath;
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

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;
        return $this;
    }

    public function getProgress(): ?string
    {
        return $this->progress;
    }

    public function setProgress(?string $progress): self
    {
        $this->progress = $progress;
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

    public function isFromAutoSnapshot(): bool
    {
        return $this->isFromAutoSnapshot;
    }

    public function setIsFromAutoSnapshot(bool $isFromAutoSnapshot): self
    {
        $this->isFromAutoSnapshot = $isFromAutoSnapshot;
        return $this;
    }

    public function getFromDiskSnapshotName(): ?string
    {
        return $this->fromDiskSnapshotName;
    }

    public function setFromDiskSnapshotName(?string $fromDiskSnapshotName): self
    {
        $this->fromDiskSnapshotName = $fromDiskSnapshotName;
        return $this;
    }

    public function getFromRegion(): ?string
    {
        return $this->fromRegion;
    }

    public function setFromRegion(?string $fromRegion): self
    {
        $this->fromRegion = $fromRegion;
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

    public function getDisk(): ?Disk
    {
        return $this->disk;
    }

    public function setDisk(?Disk $disk): self
    {
        $this->disk = $disk;
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
