<?php

namespace AwsLightsailBundle\Entity;

use AwsLightsailBundle\Enum\OperationStatusEnum;
use AwsLightsailBundle\Enum\OperationTypeEnum;
use AwsLightsailBundle\Repository\OperationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
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
    private string $operationId;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '资源名称'])]
    private ?string $resourceName = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '资源类型'])]
    private ?string $resourceType = null;

    #[ORM\Column(type: Types::STRING, length: 100, enumType: OperationTypeEnum::class, options: ['comment' => '操作类型'])]
    private OperationTypeEnum $type;

    #[ORM\Column(type: Types::STRING, length: 50, enumType: OperationStatusEnum::class, options: ['comment' => '操作状态'])]
    private OperationStatusEnum $status;

    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => 'AWS 区域'])]
    private string $region;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '错误代码'])]
    private ?string $errorCode = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '错误详情'])]
    private ?string $errorDetails = null;


    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '完成时间'])]
    private ?\DateTimeImmutable $completeTime = null;

    #[ORM\ManyToOne(targetEntity: AwsCredential::class)]
    #[ORM\JoinColumn(nullable: false)]
    private AwsCredential $credential;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '元数据'])]
    private ?array $metadata = null;

    public function __construct()
    {
        $this->status = OperationStatusEnum::UNKNOWN;
        $this->type = OperationTypeEnum::OTHER;
    }

    public function __toString(): string
    {
        return sprintf('Operation %s (%s)', $this->operationId, $this->status->value);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOperationId(): string
    {
        return $this->operationId;
    }

    public function setOperationId(string $operationId): self
    {
        $this->operationId = $operationId;
        return $this;
    }

    public function getResourceName(): ?string
    {
        return $this->resourceName;
    }

    public function setResourceName(?string $resourceName): self
    {
        $this->resourceName = $resourceName;
        return $this;
    }

    public function getResourceType(): ?string
    {
        return $this->resourceType;
    }

    public function setResourceType(?string $resourceType): self
    {
        $this->resourceType = $resourceType;
        return $this;
    }

    public function getType(): OperationTypeEnum
    {
        return $this->type;
    }

    public function setType(OperationTypeEnum $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getStatus(): OperationStatusEnum
    {
        return $this->status;
    }

    public function setStatus(OperationStatusEnum $status): self
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

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    public function setErrorCode(?string $errorCode): self
    {
        $this->errorCode = $errorCode;
        return $this;
    }

    public function getErrorDetails(): ?string
    {
        return $this->errorDetails;
    }

    public function setErrorDetails(?string $errorDetails): self
    {
        $this->errorDetails = $errorDetails;
        return $this;
    }

    public function getCompleteTime(): ?\DateTimeImmutable
    {
        return $this->completeTime;
    }

    public function setCompleteTime(?\DateTimeInterface $completeTime): self
    {
        if ($completeTime !== null && !$completeTime instanceof \DateTimeImmutable) {
            $completeTime = \DateTimeImmutable::createFromInterface($completeTime);
        }
        $this->completeTime = $completeTime;
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

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function setMetadata(?array $metadata): self
    {
        $this->metadata = $metadata;
        return $this;
    }
}
