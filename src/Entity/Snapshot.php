<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Entity;

use AwsLightsailBundle\Enum\SnapshotTypeEnum;
use AwsLightsailBundle\Repository\SnapshotRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
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
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => 'AWS ARN'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $arn;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '资源名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $resourceName;

    #[ORM\Column(type: Types::STRING, length: 50, enumType: SnapshotTypeEnum::class, options: ['comment' => '快照类型'])]
    #[Assert\Choice(callback: [SnapshotTypeEnum::class, 'cases'])]
    private SnapshotTypeEnum $type = SnapshotTypeEnum::INSTANCE;

    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => 'AWS 区域'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $region;

    /**
     * @var array<string, string>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '标签'])]
    #[Assert\Type(type: 'array')]
    private ?array $tags = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '同步时间'])]
    #[Assert\DateTime]
    private ?\DateTimeImmutable $syncTime = null;

    #[ORM\ManyToOne(targetEntity: AwsCredential::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private AwsCredential $credential;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '来源快照名称'])]
    #[Assert\Length(max: 255)]
    private ?string $fromSnapshotName = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true, options: ['comment' => '来源区域'])]
    #[Assert\Length(max: 50)]
    private ?string $fromRegion = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true, options: ['comment' => '大小(GB)'])]
    #[Assert\PositiveOrZero]
    private ?int $sizeInGb = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '状态'])]
    #[Assert\Length(max: 1000)]
    private ?string $state = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '进度'])]
    #[Assert\Length(max: 1000)]
    private ?string $progress = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否来自自动快照'])]
    #[Assert\Type(type: 'bool')]
    private bool $isFromAutoSnapshot = false;

    public function __toString(): string
    {
        return \sprintf('Snapshot %s (%s)', $this->name, $this->resourceName);
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

    public function getResourceName(): string
    {
        return $this->resourceName;
    }

    public function setResourceName(string $resourceName): void
    {
        $this->resourceName = $resourceName;
    }

    public function getType(): SnapshotTypeEnum
    {
        return $this->type;
    }

    public function setType(SnapshotTypeEnum $type): void
    {
        $this->type = $type;
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
     * @return array<string, string>|null
     */
    public function getTags(): ?array
    {
        return $this->tags;
    }

    /**
     * @param array<string, string>|null $tags
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

    public function getFromSnapshotName(): ?string
    {
        return $this->fromSnapshotName;
    }

    public function setFromSnapshotName(?string $fromSnapshotName): void
    {
        $this->fromSnapshotName = $fromSnapshotName;
    }

    public function getFromRegion(): ?string
    {
        return $this->fromRegion;
    }

    public function setFromRegion(?string $fromRegion): void
    {
        $this->fromRegion = $fromRegion;
    }

    public function getSizeInGb(): ?int
    {
        return $this->sizeInGb;
    }

    public function setSizeInGb(?int $sizeInGb): void
    {
        $this->sizeInGb = $sizeInGb;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): void
    {
        $this->state = $state;
    }

    public function getProgress(): ?string
    {
        return $this->progress;
    }

    public function setProgress(?string $progress): void
    {
        $this->progress = $progress;
    }

    public function isFromAutoSnapshot(): bool
    {
        return $this->isFromAutoSnapshot;
    }

    public function setIsFromAutoSnapshot(bool $isFromAutoSnapshot): void
    {
        $this->isFromAutoSnapshot = $isFromAutoSnapshot;
    }
}
