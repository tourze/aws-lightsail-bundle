<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Entity;

use AwsLightsailBundle\Enum\LoadBalancerStatusEnum;
use AwsLightsailBundle\Repository\LoadBalancerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: LoadBalancerRepository::class)]
#[ORM\Table(name: 'aws_lightsail_load_balancer', options: ['comment' => 'AWS Lightsail 负载均衡器表'])]
class LoadBalancer implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '主键ID'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '负载均衡器名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => 'AWS ARN'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $arn;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => 'DNS 名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $dnsName;

    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => 'AWS 区域'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $region;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '健康检查端口'])]
    #[Assert\Type(type: 'int')]
    #[Assert\Range(min: 1, max: 65535)]
    private int $healthCheckPort;

    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => '健康检查协议'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $healthCheckProtocol;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '健康检查路径'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $healthCheckPath;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '健康检查时间间隔（秒）'])]
    #[Assert\Type(type: 'int')]
    #[Assert\Range(min: 1, max: 3600)]
    private int $healthCheckIntervalSeconds;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '健康检查超时（秒）'])]
    #[Assert\Type(type: 'int')]
    #[Assert\Range(min: 1, max: 600)]
    private int $healthCheckTimeoutSeconds;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '健康阈值'])]
    #[Assert\Type(type: 'int')]
    #[Assert\Range(min: 1, max: 10)]
    private int $healthyThreshold;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '不健康阈值'])]
    #[Assert\Type(type: 'int')]
    #[Assert\Range(min: 1, max: 10)]
    private int $unhealthyThreshold;

    #[ORM\Column(type: Types::STRING, length: 50, enumType: LoadBalancerStatusEnum::class, options: ['comment' => '状态'])]
    #[Assert\Choice(callback: [LoadBalancerStatusEnum::class, 'cases'])]
    private LoadBalancerStatusEnum $status;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => 'TLS策略是否启用'])]
    #[Assert\Type(type: 'bool')]
    private bool $tlsPolicyEnabled = false;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => 'TLS证书名称'])]
    #[Assert\Length(max: 255)]
    private ?string $tlsCertificateName = null;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '实例健康状态摘要'])]
    #[Assert\Type(type: 'array')]
    private ?array $instanceHealthSummary = null;

    /**
     * @var array<string, string>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '标签'])]
    #[Assert\Type(type: 'array')]
    private ?array $tags = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '配置选项'])]
    #[Assert\Type(type: 'bool')]
    private bool $configurationOptions = false;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '同步时间'])]
    #[Assert\Type(type: \DateTimeImmutable::class)]
    private ?\DateTimeImmutable $syncTime = null;

    #[ORM\ManyToOne(targetEntity: AwsCredential::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private AwsCredential $credential;

    /**
     * @var array<int, string>
     */
    #[ORM\Column(type: Types::JSON, options: ['comment' => '已附加的实例'])]
    #[Assert\Type(type: 'array')]
    private array $attachedInstances = [];

    public function __construct()
    {
        $this->status = LoadBalancerStatusEnum::UNKNOWN;
    }

    public function __toString(): string
    {
        return \sprintf('LoadBalancer %s (%s)', $this->name, $this->status->value);
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

    public function getDnsName(): string
    {
        return $this->dnsName;
    }

    public function setDnsName(string $dnsName): void
    {
        $this->dnsName = $dnsName;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function setRegion(string $region): void
    {
        $this->region = $region;
    }

    public function getHealthCheckPort(): int
    {
        return $this->healthCheckPort;
    }

    public function setHealthCheckPort(int $healthCheckPort): void
    {
        $this->healthCheckPort = $healthCheckPort;
    }

    public function getHealthCheckProtocol(): string
    {
        return $this->healthCheckProtocol;
    }

    public function setHealthCheckProtocol(string $healthCheckProtocol): void
    {
        $this->healthCheckProtocol = $healthCheckProtocol;
    }

    public function getHealthCheckPath(): string
    {
        return $this->healthCheckPath;
    }

    public function setHealthCheckPath(string $healthCheckPath): void
    {
        $this->healthCheckPath = $healthCheckPath;
    }

    public function getHealthCheckIntervalSeconds(): int
    {
        return $this->healthCheckIntervalSeconds;
    }

    public function setHealthCheckIntervalSeconds(int $healthCheckIntervalSeconds): void
    {
        $this->healthCheckIntervalSeconds = $healthCheckIntervalSeconds;
    }

    public function getHealthCheckTimeoutSeconds(): int
    {
        return $this->healthCheckTimeoutSeconds;
    }

    public function setHealthCheckTimeoutSeconds(int $healthCheckTimeoutSeconds): void
    {
        $this->healthCheckTimeoutSeconds = $healthCheckTimeoutSeconds;
    }

    public function getHealthyThreshold(): int
    {
        return $this->healthyThreshold;
    }

    public function setHealthyThreshold(int $healthyThreshold): void
    {
        $this->healthyThreshold = $healthyThreshold;
    }

    public function getUnhealthyThreshold(): int
    {
        return $this->unhealthyThreshold;
    }

    public function setUnhealthyThreshold(int $unhealthyThreshold): void
    {
        $this->unhealthyThreshold = $unhealthyThreshold;
    }

    public function getStatus(): LoadBalancerStatusEnum
    {
        return $this->status;
    }

    public function setStatus(LoadBalancerStatusEnum $status): void
    {
        $this->status = $status;
    }

    public function isTlsPolicyEnabled(): bool
    {
        return $this->tlsPolicyEnabled;
    }

    public function setTlsPolicyEnabled(bool $tlsPolicyEnabled): void
    {
        $this->tlsPolicyEnabled = $tlsPolicyEnabled;
    }

    public function getTlsCertificateName(): ?string
    {
        return $this->tlsCertificateName;
    }

    public function setTlsCertificateName(?string $tlsCertificateName): void
    {
        $this->tlsCertificateName = $tlsCertificateName;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getInstanceHealthSummary(): ?array
    {
        return $this->instanceHealthSummary;
    }

    /**
     * @param array<string, mixed>|null $instanceHealthSummary
     */
    public function setInstanceHealthSummary(?array $instanceHealthSummary): void
    {
        $this->instanceHealthSummary = $instanceHealthSummary;
    }

    /**
     * @return array<string, string>|null
     */
    public function getTags(): ?array
    {
        return $this->tags;
    }

    /**
     * @param array<string, string>|null $tags
     */
    public function setTags(?array $tags): void
    {
        $this->tags = $tags;
    }

    public function isConfigurationOptions(): bool
    {
        return $this->configurationOptions;
    }

    public function setConfigurationOptions(bool $configurationOptions): void
    {
        $this->configurationOptions = $configurationOptions;
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
     * @return array<int, string>
     */
    public function getAttachedInstances(): array
    {
        return $this->attachedInstances;
    }

    /**
     * @param array<int, string> $attachedInstances
     */
    public function setAttachedInstances(array $attachedInstances): void
    {
        $this->attachedInstances = $attachedInstances;
    }

    public function addAttachedInstance(string $instanceName): void
    {
        if (!\in_array($instanceName, $this->attachedInstances, true)) {
            $this->attachedInstances[] = $instanceName;
        }
    }

    public function removeAttachedInstance(string $instanceName): void
    {
        $index = \array_search($instanceName, $this->attachedInstances, true);
        if (false !== $index) {
            unset($this->attachedInstances[$index]);
            $this->attachedInstances = \array_values($this->attachedInstances);
        }
    }
}
