<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Entity;

use AwsLightsailBundle\Enum\DatabaseEngineEnum;
use AwsLightsailBundle\Enum\DatabaseStatusEnum;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity]
#[ORM\Table(name: 'aws_lightsail_database', options: ['comment' => 'AWS Lightsail 数据库表'])]
class Database implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '主键ID'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '数据库名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => 'AWS ARN'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $arn;

    #[ORM\Column(type: Types::STRING, length: 50, enumType: DatabaseEngineEnum::class, options: ['comment' => '数据库引擎'])]
    #[Assert\Choice(callback: [DatabaseEngineEnum::class, 'cases'])]
    private DatabaseEngineEnum $engine;

    #[ORM\Column(type: Types::STRING, length: 20, options: ['comment' => '引擎版本'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    private string $engineVersion;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '主用户名'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $masterUsername;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '主节点终端节点'])]
    #[Assert\Length(max: 255)]
    private ?string $masterEndpoint = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '主节点端口'])]
    #[Assert\Range(min: 1, max: 65535)]
    private ?int $masterPort = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '从节点终端节点'])]
    #[Assert\Length(max: 255)]
    private ?string $secondaryEndpoint = null;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '首选备份窗口'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $preferredBackupWindow;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '首选维护窗口'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $preferredMaintenanceWindow;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否可公开访问'])]
    #[Assert\Type(type: 'bool')]
    private bool $publiclyAccessible = false;

    #[ORM\Column(type: Types::STRING, length: 50, enumType: DatabaseStatusEnum::class, options: ['comment' => '数据库状态'])]
    #[Assert\Choice(callback: [DatabaseStatusEnum::class, 'cases'])]
    private DatabaseStatusEnum $status;

    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => 'AWS 区域'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $region;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '支持代码'])]
    #[Assert\Type(type: 'bool')]
    private bool $supportCode = false;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => 'CA证书标识符'])]
    #[Assert\Length(max: 255)]
    private ?string $caCertificateIdentifier = null;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '待修改的值'])]
    #[Assert\Type(type: 'array')]
    private ?array $pendingModifiedValues = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否启用备份保留'])]
    #[Assert\Type(type: 'bool')]
    private bool $backupRetentionEnabled = false;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '标签'])]
    #[Assert\Type(type: 'array')]
    private ?array $tags = null;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '套餐ID'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $bundleId;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否自动升级小版本'])]
    #[Assert\Type(type: 'bool')]
    private bool $autoMinorVersionUpgrade = false;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '同步时间'])]
    #[Assert\DateTime]
    private ?\DateTimeImmutable $syncTime = null;

    #[ORM\ManyToOne(targetEntity: AwsCredential::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private AwsCredential $credential;

    public function __construct()
    {
        $this->status = DatabaseStatusEnum::UNKNOWN;
        $this->engine = DatabaseEngineEnum::MYSQL;
    }

    public function __toString(): string
    {
        return \sprintf('Database %s (%s %s)', $this->name, $this->engine->value, $this->status->value);
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

    public function getMasterUsername(): string
    {
        return $this->masterUsername;
    }

    public function setMasterUsername(string $masterUsername): void
    {
        $this->masterUsername = $masterUsername;
    }

    public function getMasterEndpoint(): ?string
    {
        return $this->masterEndpoint;
    }

    public function setMasterEndpoint(?string $masterEndpoint): void
    {
        $this->masterEndpoint = $masterEndpoint;
    }

    public function getMasterPort(): ?int
    {
        return $this->masterPort;
    }

    public function setMasterPort(?int $masterPort): void
    {
        $this->masterPort = $masterPort;
    }

    public function getSecondaryEndpoint(): ?string
    {
        return $this->secondaryEndpoint;
    }

    public function setSecondaryEndpoint(?string $secondaryEndpoint): void
    {
        $this->secondaryEndpoint = $secondaryEndpoint;
    }

    public function getPreferredBackupWindow(): string
    {
        return $this->preferredBackupWindow;
    }

    public function setPreferredBackupWindow(string $preferredBackupWindow): void
    {
        $this->preferredBackupWindow = $preferredBackupWindow;
    }

    public function getPreferredMaintenanceWindow(): string
    {
        return $this->preferredMaintenanceWindow;
    }

    public function setPreferredMaintenanceWindow(string $preferredMaintenanceWindow): void
    {
        $this->preferredMaintenanceWindow = $preferredMaintenanceWindow;
    }

    public function isPubliclyAccessible(): bool
    {
        return $this->publiclyAccessible;
    }

    public function setPubliclyAccessible(bool $publiclyAccessible): void
    {
        $this->publiclyAccessible = $publiclyAccessible;
    }

    public function getStatus(): DatabaseStatusEnum
    {
        return $this->status;
    }

    public function setStatus(DatabaseStatusEnum $status): void
    {
        $this->status = $status;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function setRegion(string $region): void
    {
        $this->region = $region;
    }

    public function isSupportCode(): bool
    {
        return $this->supportCode;
    }

    public function setSupportCode(bool $supportCode): void
    {
        $this->supportCode = $supportCode;
    }

    public function getCaCertificateIdentifier(): ?string
    {
        return $this->caCertificateIdentifier;
    }

    public function setCaCertificateIdentifier(?string $caCertificateIdentifier): void
    {
        $this->caCertificateIdentifier = $caCertificateIdentifier;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getPendingModifiedValues(): ?array
    {
        return $this->pendingModifiedValues;
    }

    /**
     * @param array<string, mixed>|null $pendingModifiedValues
     */
    public function setPendingModifiedValues(?array $pendingModifiedValues): void
    {
        $this->pendingModifiedValues = $pendingModifiedValues;
    }

    public function isBackupRetentionEnabled(): bool
    {
        return $this->backupRetentionEnabled;
    }

    public function setBackupRetentionEnabled(bool $backupRetentionEnabled): void
    {
        $this->backupRetentionEnabled = $backupRetentionEnabled;
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

    public function getBundleId(): string
    {
        return $this->bundleId;
    }

    public function setBundleId(string $bundleId): void
    {
        $this->bundleId = $bundleId;
    }

    public function isAutoMinorVersionUpgrade(): bool
    {
        return $this->autoMinorVersionUpgrade;
    }

    public function setAutoMinorVersionUpgrade(bool $autoMinorVersionUpgrade): void
    {
        $this->autoMinorVersionUpgrade = $autoMinorVersionUpgrade;
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
