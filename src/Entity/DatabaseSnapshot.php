<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Entity;

use AwsLightsailBundle\Enum\DatabaseEngineEnum;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity]
#[ORM\Table(name: 'aws_lightsail_database_snapshot', options: ['comment' => 'AWS Lightsail 数据库快照表'])]
class DatabaseSnapshot implements \Stringable
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

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '数据库名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $databaseName;

    #[ORM\Column(type: Types::STRING, length: 50, enumType: DatabaseEngineEnum::class, options: ['comment' => '数据库引擎'])]
    #[Assert\Choice(callback: [DatabaseEngineEnum::class, 'cases'])]
    private DatabaseEngineEnum $engine;

    #[ORM\Column(type: Types::STRING, length: 20, options: ['comment' => '引擎版本'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    private string $engineVersion;

    #[ORM\Column(type: Types::BIGINT, nullable: true, options: ['comment' => '大小(GB)'])]
    #[Assert\PositiveOrZero]
    private ?int $sizeInGb = null;

    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => 'AWS 区域'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $region;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '状态'])]
    #[Assert\Length(max: 255)]
    private ?string $state = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否来自自动快照'])]
    #[Assert\Type(type: 'bool')]
    private bool $isFromAutoSnapshot = false;

    /**
     * @var array<string, mixed>|null
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

    #[ORM\ManyToOne(targetEntity: Database::class)]
    private ?Database $database = null;

    public function __construct()
    {
        $this->engine = DatabaseEngineEnum::MYSQL;
    }

    public function __toString(): string
    {
        return \sprintf('DatabaseSnapshot %s (%s)', $this->name, $this->databaseName);
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

    public function getDatabaseName(): string
    {
        return $this->databaseName;
    }

    public function setDatabaseName(string $databaseName): void
    {
        $this->databaseName = $databaseName;
    }

    public function getEngine(): DatabaseEngineEnum
    {
        return $this->engine;
    }

    public function setEngine(DatabaseEngineEnum $engine): void
    {
        $this->engine = $engine;
    }

    public function getEngineVersion(): string
    {
        return $this->engineVersion;
    }

    public function setEngineVersion(string $engineVersion): void
    {
        $this->engineVersion = $engineVersion;
    }

    public function getSizeInGb(): ?int
    {
        return $this->sizeInGb;
    }

    public function setSizeInGb(?int $sizeInGb): void
    {
        $this->sizeInGb = $sizeInGb;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function setRegion(string $region): void
    {
        $this->region = $region;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): void
    {
        $this->state = $state;
    }

    public function isFromAutoSnapshot(): bool
    {
        return $this->isFromAutoSnapshot;
    }

    public function setIsFromAutoSnapshot(bool $isFromAutoSnapshot): void
    {
        $this->isFromAutoSnapshot = $isFromAutoSnapshot;
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

    public function getDatabase(): ?Database
    {
        return $this->database;
    }

    public function setDatabase(?Database $database): void
    {
        $this->database = $database;
    }
}
