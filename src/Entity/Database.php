<?php

namespace AwsLightsailBundle\Entity;

use AwsLightsailBundle\Enum\DatabaseEngineEnum;
use AwsLightsailBundle\Enum\DatabaseStatusEnum;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;

#[ORM\Entity]
#[ORM\Table(name: 'aws_lightsail_database', options: ['comment' => 'AWS Lightsail 数据库表'])]
class Database implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '数据库名称'])]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => 'AWS ARN'])]
    private string $arn;

    #[ORM\Column(type: 'string', length: 50, enumType: DatabaseEngineEnum::class, options: ['comment' => '数据库引擎'])]
    private DatabaseEngineEnum $engine;

    #[ORM\Column(type: 'string', length: 20, options: ['comment' => '引擎版本'])]
    private string $engineVersion;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '主用户名'])]
    private string $masterUsername;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['comment' => '主节点终端节点'])]
    private ?string $masterEndpoint = null;

    #[ORM\Column(type: 'integer', nullable: true, options: ['comment' => '主节点端口'])]
    private ?int $masterPort = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['comment' => '从节点终端节点'])]
    private ?string $secondaryEndpoint = null;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '首选备份窗口'])]
    private string $preferredBackupWindow;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '首选维护窗口'])]
    private string $preferredMaintenanceWindow;

    #[ORM\Column(type: 'boolean', options: ['comment' => '是否可公开访问'])]
    private bool $publiclyAccessible = false;

    #[ORM\Column(type: 'string', length: 50, enumType: DatabaseStatusEnum::class, options: ['comment' => '数据库状态'])]
    private DatabaseStatusEnum $status;

    #[ORM\Column(type: 'string', length: 50, options: ['comment' => 'AWS 区域'])]
    private string $region;

    #[ORM\Column(type: 'boolean', options: ['comment' => '支持代码'])]
    private bool $supportCode = false;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['comment' => 'CA证书标识符'])]
    private ?string $caCertificateIdentifier = null;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => '待修改的值'])]
    private ?array $pendingModifiedValues = null;

    #[ORM\Column(type: 'boolean', options: ['comment' => '是否启用备份保留'])]
    private bool $backupRetentionEnabled = false;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => '标签'])]
    private ?array $tags = null;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '套餐ID'])]
    private string $bundleId;

    #[ORM\Column(type: 'boolean', options: ['comment' => '是否自动升级小版本'])]
    private bool $autoMinorVersionUpgrade = false;

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
        $this->status = DatabaseStatusEnum::UNKNOWN;
        $this->engine = DatabaseEngineEnum::MYSQL;
    }

    public function __toString(): string
    {
        return sprintf('Database %s (%s %s)', $this->name, $this->engine->value, $this->status->value);
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

    public function getMasterUsername(): string
    {
        return $this->masterUsername;
    }

    public function setMasterUsername(string $masterUsername): self
    {
        $this->masterUsername = $masterUsername;
        return $this;
    }

    public function getMasterEndpoint(): ?string
    {
        return $this->masterEndpoint;
    }

    public function setMasterEndpoint(?string $masterEndpoint): self
    {
        $this->masterEndpoint = $masterEndpoint;
        return $this;
    }

    public function getMasterPort(): ?int
    {
        return $this->masterPort;
    }

    public function setMasterPort(?int $masterPort): self
    {
        $this->masterPort = $masterPort;
        return $this;
    }

    public function getSecondaryEndpoint(): ?string
    {
        return $this->secondaryEndpoint;
    }

    public function setSecondaryEndpoint(?string $secondaryEndpoint): self
    {
        $this->secondaryEndpoint = $secondaryEndpoint;
        return $this;
    }

    public function getPreferredBackupWindow(): string
    {
        return $this->preferredBackupWindow;
    }

    public function setPreferredBackupWindow(string $preferredBackupWindow): self
    {
        $this->preferredBackupWindow = $preferredBackupWindow;
        return $this;
    }

    public function getPreferredMaintenanceWindow(): string
    {
        return $this->preferredMaintenanceWindow;
    }

    public function setPreferredMaintenanceWindow(string $preferredMaintenanceWindow): self
    {
        $this->preferredMaintenanceWindow = $preferredMaintenanceWindow;
        return $this;
    }

    public function isPubliclyAccessible(): bool
    {
        return $this->publiclyAccessible;
    }

    public function setPubliclyAccessible(bool $publiclyAccessible): self
    {
        $this->publiclyAccessible = $publiclyAccessible;
        return $this;
    }

    public function getStatus(): DatabaseStatusEnum
    {
        return $this->status;
    }

    public function setStatus(DatabaseStatusEnum $status): self
    {
        $this->status = $status;
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

    public function isSupportCode(): bool
    {
        return $this->supportCode;
    }

    public function setSupportCode(bool $supportCode): self
    {
        $this->supportCode = $supportCode;
        return $this;
    }

    public function getCaCertificateIdentifier(): ?string
    {
        return $this->caCertificateIdentifier;
    }

    public function setCaCertificateIdentifier(?string $caCertificateIdentifier): self
    {
        $this->caCertificateIdentifier = $caCertificateIdentifier;
        return $this;
    }

    public function getPendingModifiedValues(): ?array
    {
        return $this->pendingModifiedValues;
    }

    public function setPendingModifiedValues(?array $pendingModifiedValues): self
    {
        $this->pendingModifiedValues = $pendingModifiedValues;
        return $this;
    }

    public function isBackupRetentionEnabled(): bool
    {
        return $this->backupRetentionEnabled;
    }

    public function setBackupRetentionEnabled(bool $backupRetentionEnabled): self
    {
        $this->backupRetentionEnabled = $backupRetentionEnabled;
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

    public function getBundleId(): string
    {
        return $this->bundleId;
    }

    public function setBundleId(string $bundleId): self
    {
        $this->bundleId = $bundleId;
        return $this;
    }

    public function isAutoMinorVersionUpgrade(): bool
    {
        return $this->autoMinorVersionUpgrade;
    }

    public function setAutoMinorVersionUpgrade(bool $autoMinorVersionUpgrade): self
    {
        $this->autoMinorVersionUpgrade = $autoMinorVersionUpgrade;
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
