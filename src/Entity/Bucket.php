<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Entity;

use AwsLightsailBundle\Enum\BucketAccessRuleEnum;
use AwsLightsailBundle\Repository\BucketRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: BucketRepository::class)]
#[ORM\Table(name: 'aws_lightsail_bucket', options: ['comment' => 'AWS Lightsail 存储桶表'])]
class Bucket implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '主键ID'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '存储桶名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => 'AWS ARN'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $arn;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => 'URL'])]
    #[Assert\Length(max: 255)]
    #[Assert\Url]
    private ?string $url = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '套餐ID'])]
    #[Assert\Length(max: 255)]
    private ?string $bundleId = null;

    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => 'AWS 区域'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $region;

    #[ORM\Column(type: Types::STRING, length: 50, enumType: BucketAccessRuleEnum::class, options: ['comment' => '访问规则'])]
    #[Assert\Choice(callback: [BucketAccessRuleEnum::class, 'cases'])]
    private BucketAccessRuleEnum $accessRules;

    /**
     * @var array<string>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '只读访问账户'])]
    #[Assert\Type(type: 'array')]
    private ?array $readonlyAccessAccounts = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否开启版本控制'])]
    #[Assert\Type(type: 'bool')]
    private bool $isVersioning = false;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '对象版本控制'])]
    #[Assert\Type(type: 'bool')]
    private bool $objectVersioning = false;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否为资源类型'])]
    #[Assert\Type(type: 'bool')]
    private bool $isResourceType = false;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '标签'])]
    #[Assert\Type(type: 'array')]
    private ?array $tags = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '大小(MB)'])]
    #[Assert\PositiveOrZero]
    private ?int $sizeInMb = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '对象数量'])]
    #[Assert\PositiveOrZero]
    private ?int $objectCount = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '同步时间'])]
    #[Assert\DateTime]
    private ?\DateTimeImmutable $syncTime = null;

    #[ORM\ManyToOne(targetEntity: AwsCredential::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private AwsCredential $credential;

    /**
     * @var array<int, array{rule: string, allowedOrigins: array<string>}>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => 'CORS规则'])]
    #[Assert\Type(type: 'array')]
    private ?array $corsRules = null;

    public function __construct()
    {
        $this->accessRules = BucketAccessRuleEnum::PRIVATE;
    }

    public function __toString(): string
    {
        return \sprintf('Bucket %s (%s)', $this->name, $this->region);
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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    public function getBundleId(): ?string
    {
        return $this->bundleId;
    }

    public function setBundleId(?string $bundleId): void
    {
        $this->bundleId = $bundleId;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function setRegion(string $region): void
    {
        $this->region = $region;
    }

    public function getAccessRules(): BucketAccessRuleEnum
    {
        return $this->accessRules;
    }

    public function setAccessRules(BucketAccessRuleEnum $accessRules): void
    {
        $this->accessRules = $accessRules;
    }

    /**
     * @return string[]|null
     */
    public function getReadonlyAccessAccounts(): ?array
    {
        return $this->readonlyAccessAccounts;
    }

    /**
     * @param string[]|null $readonlyAccessAccounts
     */
    public function setReadonlyAccessAccounts(?array $readonlyAccessAccounts): void
    {
        $this->readonlyAccessAccounts = $readonlyAccessAccounts;
    }

    public function isVersioning(): bool
    {
        return $this->isVersioning;
    }

    public function setIsVersioning(bool $isVersioning): void
    {
        $this->isVersioning = $isVersioning;
    }

    public function isObjectVersioning(): bool
    {
        return $this->objectVersioning;
    }

    public function setObjectVersioning(bool $objectVersioning): void
    {
        $this->objectVersioning = $objectVersioning;
    }

    public function isResourceType(): bool
    {
        return $this->isResourceType;
    }

    public function setIsResourceType(bool $isResourceType): void
    {
        $this->isResourceType = $isResourceType;
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

    public function getSizeInMb(): ?int
    {
        return $this->sizeInMb;
    }

    public function setSizeInMb(?int $sizeInMb): void
    {
        $this->sizeInMb = $sizeInMb;
    }

    public function getObjectCount(): ?int
    {
        return $this->objectCount;
    }

    public function setObjectCount(?int $objectCount): void
    {
        $this->objectCount = $objectCount;
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

    /**
     * @return array<int, array{rule: string, allowedOrigins: string[]}>|null
     */
    public function getCorsRules(): ?array
    {
        return $this->corsRules;
    }

    /**
     * @param array<int, array{rule: string, allowedOrigins: string[]}>|null $corsRules
     */
    public function setCorsRules(?array $corsRules): void
    {
        $this->corsRules = $corsRules;
    }
}
