<?php

namespace AwsLightsailBundle\Entity;

use AwsLightsailBundle\Enum\BucketAccessRuleEnum;
use AwsLightsailBundle\Repository\BucketRepository;
use Carbon\Carbon;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;

#[ORM\Entity(repositoryClass: BucketRepository::class)]
#[ORM\Table(name: 'aws_lightsail_bucket', options: ['comment' => 'AWS Lightsail 存储桶表'])]
class Bucket implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '存储桶名称'])]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => 'AWS ARN'])]
    private string $arn;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['comment' => 'URL'])]
    private ?string $url = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['comment' => '套餐ID'])]
    private ?string $bundleId = null;

    #[ORM\Column(type: 'string', length: 50, options: ['comment' => 'AWS 区域'])]
    private string $region;

    #[ORM\Column(type: 'string', length: 50, enumType: BucketAccessRuleEnum::class, options: ['comment' => '访问规则'])]
    private BucketAccessRuleEnum $accessRules;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => '只读访问账户'])]
    private ?array $readonlyAccessAccounts = null;

    #[ORM\Column(type: 'boolean', options: ['comment' => '是否开启版本控制'])]
    private bool $isVersioning = false;

    #[ORM\Column(type: 'boolean', options: ['comment' => '对象版本控制'])]
    private bool $objectVersioning = false;

    #[ORM\Column(type: 'boolean', options: ['comment' => '是否为资源类型'])]
    private bool $isResourceType = false;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => '标签'])]
    private ?array $tags = null;

    #[ORM\Column(type: 'integer', nullable: true, options: ['comment' => '大小(MB)'])]
    private ?int $sizeInMb = null;

    #[ORM\Column(type: 'integer', nullable: true, options: ['comment' => '对象数量'])]
    private ?int $objectCount = null;

    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['comment' => '创建时间'])]
    private \DateTimeInterface $createTime;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '同步时间'])]
    private ?\DateTimeInterface $syncTime = null;

    #[ORM\ManyToOne(targetEntity: AwsCredential::class)]
    #[ORM\JoinColumn(nullable: false)]
    private AwsCredential $credential;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => 'CORS规则'])]
    private ?array $corsRules = null;

    #[UpdateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

    public function __construct()
    {
        $this->createTime = Carbon::now();
        $this->accessRules = BucketAccessRuleEnum::PRIVATE;
    }

    public function __toString(): string
    {
        return sprintf('Bucket %s (%s)', $this->name, $this->region);
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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function getBundleId(): ?string
    {
        return $this->bundleId;
    }

    public function setBundleId(?string $bundleId): self
    {
        $this->bundleId = $bundleId;
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

    public function getAccessRules(): BucketAccessRuleEnum
    {
        return $this->accessRules;
    }

    public function setAccessRules(BucketAccessRuleEnum $accessRules): self
    {
        $this->accessRules = $accessRules;
        return $this;
    }

    public function getReadonlyAccessAccounts(): ?array
    {
        return $this->readonlyAccessAccounts;
    }

    public function setReadonlyAccessAccounts(?array $readonlyAccessAccounts): self
    {
        $this->readonlyAccessAccounts = $readonlyAccessAccounts;
        return $this;
    }

    public function isVersioning(): bool
    {
        return $this->isVersioning;
    }

    public function setIsVersioning(bool $isVersioning): self
    {
        $this->isVersioning = $isVersioning;
        return $this;
    }

    public function isObjectVersioning(): bool
    {
        return $this->objectVersioning;
    }

    public function setObjectVersioning(bool $objectVersioning): self
    {
        $this->objectVersioning = $objectVersioning;
        return $this;
    }

    public function isResourceType(): bool
    {
        return $this->isResourceType;
    }

    public function setIsResourceType(bool $isResourceType): self
    {
        $this->isResourceType = $isResourceType;
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

    public function getSizeInMb(): ?int
    {
        return $this->sizeInMb;
    }

    public function setSizeInMb(?int $sizeInMb): self
    {
        $this->sizeInMb = $sizeInMb;
        return $this;
    }

    public function getObjectCount(): ?int
    {
        return $this->objectCount;
    }

    public function setObjectCount(?int $objectCount): self
    {
        $this->objectCount = $objectCount;
        return $this;
    }

    public function getCreateTime(): \DateTimeInterface
    {
        return $this->createTime;
    }

    public function getSyncTime(): ?\DateTimeInterface
    {
        return $this->syncTime;
    }

    public function setSyncTime(?\DateTimeInterface $syncTime): self
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

    public function getCorsRules(): ?array
    {
        return $this->corsRules;
    }

    public function setCorsRules(?array $corsRules): self
    {
        $this->corsRules = $corsRules;
        return $this;
    }

    public function getUpdateTime(): ?\DateTimeInterface
    {
        return $this->updateTime;
    }

    public function setUpdateTime(?\DateTimeInterface $updateTime): self
    {
        $this->updateTime = $updateTime;
        return $this;
    }
}
