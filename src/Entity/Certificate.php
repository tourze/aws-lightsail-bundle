<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Entity;

use AwsLightsailBundle\Enum\CertificateStatusEnum;
use AwsLightsailBundle\Repository\CertificateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: CertificateRepository::class)]
#[ORM\Table(name: 'aws_lightsail_certificate', options: ['comment' => 'AWS Lightsail 证书表'])]
class Certificate implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '主键ID'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '证书名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => 'AWS ARN'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $arn;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '域名'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $domainName;

    /**
     * @var array<string>
     */
    #[ORM\Column(type: Types::JSON, options: ['comment' => '备用域名'])]
    #[Assert\Type(type: 'array')]
    private array $subjectAlternativeNames = [];

    /**
     * @var array<int, array{domainName: string, resourceRecord: array{name: string, type: string, value: string}}>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '域名验证记录'])]
    #[Assert\Type(type: 'array')]
    private ?array $domainValidationRecords = null;

    #[ORM\Column(type: Types::STRING, length: 50, enumType: CertificateStatusEnum::class, options: ['comment' => '证书状态'])]
    #[Assert\Choice(callback: [CertificateStatusEnum::class, 'cases'])]
    private CertificateStatusEnum $status;

    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => 'AWS 区域'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $region;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '证书生效日期'])]
    #[Assert\DateTime]
    private ?\DateTimeImmutable $validFromTime = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '证书过期日期'])]
    #[Assert\DateTime]
    private ?\DateTimeImmutable $validToTime = null;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '标签'])]
    #[Assert\Type(type: 'array')]
    private ?array $tags = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '序列号'])]
    #[Assert\Length(max: 255)]
    private ?string $serialNumber = null;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '密钥算法'])]
    #[Assert\Type(type: 'array')]
    private ?array $keyAlgorithm = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否由 Lightsail 管理'])]
    #[Assert\Type(type: 'bool')]
    private bool $isManaged = true;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否正在使用'])]
    #[Assert\Type(type: 'bool')]
    private bool $inUse = false;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '同步时间'])]
    #[Assert\DateTime]
    private ?\DateTimeImmutable $syncTime = null;

    #[ORM\ManyToOne(targetEntity: AwsCredential::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private AwsCredential $credential;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '支持的资源'])]
    #[Assert\Type(type: 'array')]
    private ?array $supportedOnResources = null;

    public function __construct()
    {
        $this->status = CertificateStatusEnum::UNKNOWN;
    }

    public function __toString(): string
    {
        return \sprintf('Certificate %s (%s)', $this->name, $this->domainName);
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

    public function getDomainName(): string
    {
        return $this->domainName;
    }

    public function setDomainName(string $domainName): void
    {
        $this->domainName = $domainName;
    }

    /**
     * @return string[]
     */
    public function getSubjectAlternativeNames(): array
    {
        return $this->subjectAlternativeNames;
    }

    /**
     * @param string[] $subjectAlternativeNames
     */
    public function setSubjectAlternativeNames(array $subjectAlternativeNames): void
    {
        $this->subjectAlternativeNames = $subjectAlternativeNames;
    }

    /**
     * @return array<int, array{domainName: string, resourceRecord: array{name: string, type: string, value: string}}>|null
     */
    public function getDomainValidationRecords(): ?array
    {
        return $this->domainValidationRecords;
    }

    /**
     * @param array<int, array{domainName: string, resourceRecord: array{name: string, type: string, value: string}}>|null $domainValidationRecords
     */
    public function setDomainValidationRecords(?array $domainValidationRecords): void
    {
        $this->domainValidationRecords = $domainValidationRecords;
    }

    public function getStatus(): CertificateStatusEnum
    {
        return $this->status;
    }

    public function setStatus(CertificateStatusEnum $status): void
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

    public function getValidFromTime(): ?\DateTimeImmutable
    {
        return $this->validFromTime;
    }

    public function setValidFromTime(?\DateTimeInterface $validFromTime): void
    {
        if (null !== $validFromTime && !$validFromTime instanceof \DateTimeImmutable) {
            $validFromTime = \DateTimeImmutable::createFromInterface($validFromTime);
        }
        $this->validFromTime = $validFromTime;
    }

    public function getValidToTime(): ?\DateTimeImmutable
    {
        return $this->validToTime;
    }

    public function setValidToTime(?\DateTimeInterface $validToTime): void
    {
        if (null !== $validToTime && !$validToTime instanceof \DateTimeImmutable) {
            $validToTime = \DateTimeImmutable::createFromInterface($validToTime);
        }
        $this->validToTime = $validToTime;
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

    public function getSerialNumber(): ?string
    {
        return $this->serialNumber;
    }

    public function setSerialNumber(?string $serialNumber): void
    {
        $this->serialNumber = $serialNumber;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getKeyAlgorithm(): ?array
    {
        return $this->keyAlgorithm;
    }

    /**
     * @param array<string, mixed>|null $keyAlgorithm
     */
    public function setKeyAlgorithm(?array $keyAlgorithm): void
    {
        $this->keyAlgorithm = $keyAlgorithm;
    }

    public function isManaged(): bool
    {
        return $this->isManaged;
    }

    public function setIsManaged(bool $isManaged): void
    {
        $this->isManaged = $isManaged;
    }

    public function isInUse(): bool
    {
        return $this->inUse;
    }

    public function setInUse(bool $inUse): void
    {
        $this->inUse = $inUse;
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
     * @return array<string, mixed>|null
     */
    public function getSupportedOnResources(): ?array
    {
        return $this->supportedOnResources;
    }

    /**
     * @param array<string, mixed>|null $supportedOnResources
     */
    public function setSupportedOnResources(?array $supportedOnResources): void
    {
        $this->supportedOnResources = $supportedOnResources;
    }
}
