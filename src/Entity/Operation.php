<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Entity;

use AwsLightsailBundle\Enum\OperationStatusEnum;
use AwsLightsailBundle\Enum\OperationTypeEnum;
use AwsLightsailBundle\Repository\OperationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: OperationRepository::class)]
#[ORM\Table(name: 'aws_lightsail_operation', options: ['comment' => 'AWS Lightsail 操作记录表'])]
class Operation implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '主键ID'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '操作ID'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $operationId;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '资源名称'])]
    #[Assert\Length(max: 255)]
    private ?string $resourceName = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '资源类型'])]
    #[Assert\Length(max: 255)]
    private ?string $resourceType = null;

    #[ORM\Column(type: Types::STRING, length: 100, enumType: OperationTypeEnum::class, options: ['comment' => '操作类型'])]
    #[Assert\Choice(callback: [OperationTypeEnum::class, 'cases'])]
    private OperationTypeEnum $type;

    #[ORM\Column(type: Types::STRING, length: 50, enumType: OperationStatusEnum::class, options: ['comment' => '操作状态'])]
    #[Assert\Choice(callback: [OperationStatusEnum::class, 'cases'])]
    private OperationStatusEnum $status;

    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => 'AWS 区域'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $region;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '错误代码'])]
    #[Assert\Length(max: 65535)]
    private ?string $errorCode = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '错误详情'])]
    #[Assert\Length(max: 65535)]
    private ?string $errorDetails = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '完成时间'])]
    #[Assert\Type(type: \DateTimeImmutable::class)]
    private ?\DateTimeImmutable $completionTime = null;

    #[ORM\ManyToOne(targetEntity: AwsCredential::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private AwsCredential $credential;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '元数据'])]
    #[Assert\Type(type: 'array')]
    private ?array $metadata = null;

    public function __construct()
    {
        $this->status = OperationStatusEnum::UNKNOWN;
        $this->type   = OperationTypeEnum::OTHER;
    }

    public function __toString(): string
    {
        return \sprintf('Operation %s (%s)', $this->operationId, $this->status->value);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOperationId(): string
    {
        return $this->operationId;
    }

    public function setOperationId(string $operationId): void
    {
        $this->operationId = $operationId;
    }

    public function getResourceName(): ?string
    {
        return $this->resourceName;
    }

    public function setResourceName(?string $resourceName): void
    {
        $this->resourceName = $resourceName;
    }

    public function getResourceType(): ?string
    {
        return $this->resourceType;
    }

    public function setResourceType(?string $resourceType): void
    {
        $this->resourceType = $resourceType;
    }

    public function getType(): OperationTypeEnum
    {
        return $this->type;
    }

    public function setType(OperationTypeEnum $type): void
    {
        $this->type = $type;
    }

    public function getStatus(): OperationStatusEnum
    {
        return $this->status;
    }

    public function setStatus(OperationStatusEnum $status): void
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

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    public function setErrorCode(?string $errorCode): void
    {
        $this->errorCode = $errorCode;
    }

    public function getErrorDetails(): ?string
    {
        return $this->errorDetails;
    }

    public function setErrorDetails(?string $errorDetails): void
    {
        $this->errorDetails = $errorDetails;
    }

    public function getCompletionTime(): ?\DateTimeImmutable
    {
        return $this->completionTime;
    }

    public function setCompletionTime(?\DateTimeInterface $completionTime): void
    {
        if (null !== $completionTime && !$completionTime instanceof \DateTimeImmutable) {
            $completionTime = \DateTimeImmutable::createFromInterface($completionTime);
        }
        $this->completionTime = $completionTime;
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
    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    /**
     * @param array<string, mixed>|null $metadata
     */
    public function setMetadata(?array $metadata): void
    {
        $this->metadata = $metadata;
    }
}
