<?php

namespace AwsLightsailBundle\Entity;

use AwsLightsailBundle\Enum\SnapshotTypeEnum;
use AwsLightsailBundle\Repository\SnapshotRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: SnapshotRepository::class)]
#[ORM\Table(name: 'aws_lightsail_snapshot', options: ['comment' => 'AWS Lightsail 实例快照表'])]
class Snapshot implements \Stringable
{
    use TimestampableAware;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '主键ID'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '快照名称'])]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => 'AWS ARN'])]
    private string $arn;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '资源名称'])]
    private string $resourceName;

    #[ORM\Column(type: Types::STRING, length: 50, enumType: SnapshotTypeEnum::class, options: ['comment' => '快照类型'])]
    private SnapshotTypeEnum $type = SnapshotTypeEnum::INSTANCE;

    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => 'AWS 区域'])]
    private string $region;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '标签'])]
    private ?array $tags = null;


    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '同步时间'])]
    private ?\DateTimeImmutable $syncTime = null;

    #[ORM\ManyToOne(targetEntity: AwsCredential::class)]
    #[ORM\JoinColumn(nullable: false)]
    private AwsCredential $credential;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '来源快照名称'])]
    private ?string $fromSnapshotName = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true, options: ['comment' => '来源区域'])]
    private ?string $fromRegion = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true, options: ['comment' => '大小(GB)'])]
    private ?int $sizeInGb = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '状态'])]
    private ?string $state = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '进度'])]
    private ?string $progress = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否来自自动快照'])]
    private bool $isFromAutoSnapshot = false;


    public function __toString(): string
    {
        return sprintf('Snapshot %s (%s)', $this->name, $this->resourceName);
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

    public function getResourceName(): string
    {
        return $this->resourceName;
    }

    public function setResourceName(string $resourceName): self
    {
        $this->resourceName = $resourceName;
        return $this;
    }

    public function getType(): SnapshotTypeEnum
    {
        return $this->type;
    }

    public function setType(SnapshotTypeEnum $type): self
    {
        $this->type = $type;
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

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function setTags(?array $tags): self
    {
        $this->tags = $tags;
        return $this;
    }

    public function getSyncTime(): ?\DateTimeImmutable
    {
        return $this->syncTime;
    }

    public function setSyncTime(?\DateTimeInterface $syncTime): self
    {
        if ($syncTime !== null && !$syncTime instanceof \DateTimeImmutable) {
            $syncTime = \DateTimeImmutable::createFromInterface($syncTime);
        }
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

    public function getFromSnapshotName(): ?string
    {
        return $this->fromSnapshotName;
    }

    public function setFromSnapshotName(?string $fromSnapshotName): self
    {
        $this->fromSnapshotName = $fromSnapshotName;
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

    public function getSizeInGb(): ?int
    {
        return $this->sizeInGb;
    }

    public function setSizeInGb(?int $sizeInGb): self
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

    public function isFromAutoSnapshot(): bool
    {
        return $this->isFromAutoSnapshot;
    }

    public function setIsFromAutoSnapshot(bool $isFromAutoSnapshot): self
    {
        $this->isFromAutoSnapshot = $isFromAutoSnapshot;
        return $this;
    }}
