<?php

namespace AwsLightsailBundle\Entity;

use AwsLightsailBundle\Enum\CertificateStatusEnum;
use AwsLightsailBundle\Repository\CertificateRepository;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;

#[ORM\Entity(repositoryClass: CertificateRepository::class)]
#[ORM\Table(name: 'aws_lightsail_certificate', options: ['comment' => 'AWS Lightsail 证书表'])]
class Certificate implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '证书名称'])]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => 'AWS ARN'])]
    private string $arn;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '域名'])]
    private string $domainName;

    #[ORM\Column(type: 'json', options: ['comment' => '备用域名'])]
    private array $subjectAlternativeNames = [];

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => '域名验证记录'])]
    private ?array $domainValidationRecords = null;

    #[ORM\Column(type: 'string', length: 50, enumType: CertificateStatusEnum::class, options: ['comment' => '证书状态'])]
    private CertificateStatusEnum $status;

    #[ORM\Column(type: 'string', length: 50, options: ['comment' => 'AWS 区域'])]
    private string $region;

    #[ORM\Column(type: 'datetime_immutable', nullable: true, options: ['comment' => '证书生效日期'])]
    private ?\DateTimeImmutable $notBefore = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true, options: ['comment' => '证书过期日期'])]
    private ?\DateTimeImmutable $notAfter = null;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => '标签'])]
    private ?array $tags = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['comment' => '序列号'])]
    private ?string $serialNumber = null;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => '密钥算法'])]
    private ?array $keyAlgorithm = null;

    #[ORM\Column(type: 'boolean', options: ['comment' => '是否由 Lightsail 管理'])]
    private bool $isManaged = true;

    #[ORM\Column(type: 'boolean', options: ['comment' => '是否正在使用'])]
    private bool $inUse = false;

    #[CreateTimeColumn]
    #[ORM\Column(type: 'datetime_immutable', options: ['comment' => '创建时间'])]
    private \DateTimeImmutable $createTime;

    #[ORM\Column(type: 'datetime_immutable', nullable: true, options: ['comment' => '同步时间'])]
    private ?\DateTimeImmutable $syncTime = null;

    #[ORM\ManyToOne(targetEntity: AwsCredential::class)]
    #[ORM\JoinColumn(nullable: false)]
    private AwsCredential $credential;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => '支持的资源'])]
    private ?array $supportedOnResources = null;

    #[UpdateTimeColumn]
    #[ORM\Column(type: 'datetime_immutable', nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeImmutable $updateTime = null;

    public function __construct()
    {
        $this->createTime = new \DateTimeImmutable();
        $this->status = CertificateStatusEnum::UNKNOWN;
    }

    public function __toString(): string
    {
        return sprintf('Certificate %s (%s)', $this->name, $this->domainName);
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

    public function getDomainName(): string
    {
        return $this->domainName;
    }

    public function setDomainName(string $domainName): self
    {
        $this->domainName = $domainName;
        return $this;
    }

    public function getSubjectAlternativeNames(): array
    {
        return $this->subjectAlternativeNames;
    }

    public function setSubjectAlternativeNames(array $subjectAlternativeNames): self
    {
        $this->subjectAlternativeNames = $subjectAlternativeNames;
        return $this;
    }

    public function getDomainValidationRecords(): ?array
    {
        return $this->domainValidationRecords;
    }

    public function setDomainValidationRecords(?array $domainValidationRecords): self
    {
        $this->domainValidationRecords = $domainValidationRecords;
        return $this;
    }

    public function getStatus(): CertificateStatusEnum
    {
        return $this->status;
    }

    public function setStatus(CertificateStatusEnum $status): self
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

    public function getNotBefore(): ?\DateTimeImmutable
    {
        return $this->notBefore;
    }

    public function setNotBefore(?\DateTimeImmutable $notBefore): self
    {
        $this->notBefore = $notBefore;
        return $this;
    }

    public function getNotAfter(): ?\DateTimeImmutable
    {
        return $this->notAfter;
    }

    public function setNotAfter(?\DateTimeImmutable $notAfter): self
    {
        $this->notAfter = $notAfter;
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

    public function getSerialNumber(): ?string
    {
        return $this->serialNumber;
    }

    public function setSerialNumber(?string $serialNumber): self
    {
        $this->serialNumber = $serialNumber;
        return $this;
    }

    public function getKeyAlgorithm(): ?array
    {
        return $this->keyAlgorithm;
    }

    public function setKeyAlgorithm(?array $keyAlgorithm): self
    {
        $this->keyAlgorithm = $keyAlgorithm;
        return $this;
    }

    public function isManaged(): bool
    {
        return $this->isManaged;
    }

    public function setIsManaged(bool $isManaged): self
    {
        $this->isManaged = $isManaged;
        return $this;
    }

    public function isInUse(): bool
    {
        return $this->inUse;
    }

    public function setInUse(bool $inUse): self
    {
        $this->inUse = $inUse;
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

    public function getSupportedOnResources(): ?array
    {
        return $this->supportedOnResources;
    }

    public function setSupportedOnResources(?array $supportedOnResources): self
    {
        $this->supportedOnResources = $supportedOnResources;
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
