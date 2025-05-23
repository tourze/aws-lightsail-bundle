<?php

namespace AwsLightsailBundle\Entity;

use AwsLightsailBundle\Enum\DatabaseEngineEnum;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;

#[ORM\Entity]
#[ORM\Table(name: 'aws_lightsail_database_snapshot', options: ['comment' => 'AWS Lightsail 数据库快照表'])]
class DatabaseSnapshot implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '快照名称'])]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => 'AWS ARN'])]
    private string $arn;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '数据库名称'])]
    private string $databaseName;

    #[ORM\Column(type: 'string', length: 50, enumType: DatabaseEngineEnum::class, options: ['comment' => '数据库引擎'])]
    private DatabaseEngineEnum $engine;

    #[ORM\Column(type: 'string', length: 20, options: ['comment' => '引擎版本'])]
    private string $engineVersion;

    #[ORM\Column(type: 'bigint', nullable: true, options: ['comment' => '大小(GB)'])]
    private ?int $sizeInGb = null;

    #[ORM\Column(type: 'string', length: 50, options: ['comment' => 'AWS 区域'])]
    private string $region;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['comment' => '状态'])]
    private ?string $state = null;

    #[ORM\Column(type: 'boolean', options: ['comment' => '是否来自自动快照'])]
    private bool $isFromAutoSnapshot = false;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => '标签'])]
    private ?array $tags = null;

    #[CreateTimeColumn]
    #[ORM\Column(type: 'datetime_immutable', options: ['comment' => '创建时间'])]
    private \DateTimeImmutable $createTime;

    #[ORM\Column(type: 'datetime_immutable', nullable: true, options: ['comment' => '同步时间'])]
    private ?\DateTimeImmutable $syncTime = null;

    #[ORM\ManyToOne(targetEntity: AwsCredential::class)]
    #[ORM\JoinColumn(nullable: false)]
    private AwsCredential $credential;

    #[ORM\ManyToOne(targetEntity: Database::class)]
    private ?Database $database = null;

    #[UpdateTimeColumn]
    #[ORM\Column(type: 'datetime_immutable', nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeImmutable $updateTime = null;

    public function __construct()
    {
        $this->createTime = new \DateTimeImmutable();
        $this->engine = DatabaseEngineEnum::MYSQL;
    }

    public function __toString(): string
    {
        return sprintf('DatabaseSnapshot %s (%s)', $this->name, $this->databaseName);
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

    public function getDatabaseName(): string
    {
        return $this->databaseName;
    }

    public function setDatabaseName(string $databaseName): self
    {
        $this->databaseName = $databaseName;
        return $this;
    }

    public function getEngine(): DatabaseEngineEnum
    {
        return $this->engine;
    }

    public function setEngine(DatabaseEngineEnum $engine): self
    {
        $this->engine = $engine;
        return $this;
    }

    public function getEngineVersion(): string
    {
        return $this->engineVersion;
    }

    public function setEngineVersion(string $engineVersion): self
    {
        $this->engineVersion = $engineVersion;
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

    public function getRegion(): string
    {
        return $this->region;
    }

    public function setRegion(string $region): self
    {
        $this->region = $region;
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

    public function isFromAutoSnapshot(): bool
    {
        return $this->isFromAutoSnapshot;
    }

    public function setIsFromAutoSnapshot(bool $isFromAutoSnapshot): self
    {
        $this->isFromAutoSnapshot = $isFromAutoSnapshot;
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

    public function getDatabase(): ?Database
    {
        return $this->database;
    }

    public function setDatabase(?Database $database): self
    {
        $this->database = $database;
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
