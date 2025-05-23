<?php

namespace AwsLightsailBundle\Entity;

use AwsLightsailBundle\Enum\LoadBalancerStatusEnum;
use AwsLightsailBundle\Repository\LoadBalancerRepository;
use Carbon\Carbon;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;

#[ORM\Entity(repositoryClass: LoadBalancerRepository::class)]
#[ORM\Table(name: 'aws_lightsail_load_balancer', options: ['comment' => 'AWS Lightsail 负载均衡器表'])]
class LoadBalancer implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '负载均衡器名称'])]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => 'AWS ARN'])]
    private string $arn;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => 'DNS 名称'])]
    private string $dnsName;

    #[ORM\Column(type: 'string', length: 50, options: ['comment' => 'AWS 区域'])]
    private string $region;

    #[ORM\Column(type: 'integer', options: ['comment' => '健康检查端口'])]
    private int $healthCheckPort;

    #[ORM\Column(type: 'string', length: 50, options: ['comment' => '健康检查协议'])]
    private string $healthCheckProtocol;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '健康检查路径'])]
    private string $healthCheckPath;

    #[ORM\Column(type: 'integer', options: ['comment' => '健康检查时间间隔（秒）'])]
    private int $healthCheckIntervalSeconds;

    #[ORM\Column(type: 'integer', options: ['comment' => '健康检查超时（秒）'])]
    private int $healthCheckTimeoutSeconds;

    #[ORM\Column(type: 'integer', options: ['comment' => '健康阈值'])]
    private int $healthyThreshold;

    #[ORM\Column(type: 'integer', options: ['comment' => '不健康阈值'])]
    private int $unhealthyThreshold;

    #[ORM\Column(type: 'string', length: 50, enumType: LoadBalancerStatusEnum::class, options: ['comment' => '状态'])]
    private LoadBalancerStatusEnum $status;

    #[ORM\Column(type: 'boolean', options: ['comment' => 'TLS策略是否启用'])]
    private bool $tlsPolicyEnabled = false;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['comment' => 'TLS证书名称'])]
    private ?string $tlsCertificateName = null;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => '实例健康状态摘要'])]
    private ?array $instanceHealthSummary = null;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => '标签'])]
    private ?array $tags = null;

    #[ORM\Column(type: 'boolean', options: ['comment' => '配置选项'])]
    private bool $configurationOptions = false;

    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['comment' => '创建时间'])]
    private \DateTimeInterface $createTime;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '同步时间'])]
    private ?\DateTimeInterface $syncTime = null;

    #[ORM\ManyToOne(targetEntity: AwsCredential::class)]
    #[ORM\JoinColumn(nullable: false)]
    private AwsCredential $credential;

    #[ORM\Column(type: 'json', options: ['comment' => '已附加的实例'])]
    private array $attachedInstances = [];

    #[UpdateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

    public function __construct()
    {
        $this->createTime = Carbon::now();
        $this->status = LoadBalancerStatusEnum::UNKNOWN;
    }

    public function __toString(): string
    {
        return sprintf('LoadBalancer %s (%s)', $this->name, $this->status->value);
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

    public function getDnsName(): string
    {
        return $this->dnsName;
    }

    public function setDnsName(string $dnsName): self
    {
        $this->dnsName = $dnsName;
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

    public function getHealthCheckPort(): int
    {
        return $this->healthCheckPort;
    }

    public function setHealthCheckPort(int $healthCheckPort): self
    {
        $this->healthCheckPort = $healthCheckPort;
        return $this;
    }

    public function getHealthCheckProtocol(): string
    {
        return $this->healthCheckProtocol;
    }

    public function setHealthCheckProtocol(string $healthCheckProtocol): self
    {
        $this->healthCheckProtocol = $healthCheckProtocol;
        return $this;
    }

    public function getHealthCheckPath(): string
    {
        return $this->healthCheckPath;
    }

    public function setHealthCheckPath(string $healthCheckPath): self
    {
        $this->healthCheckPath = $healthCheckPath;
        return $this;
    }

    public function getHealthCheckIntervalSeconds(): int
    {
        return $this->healthCheckIntervalSeconds;
    }

    public function setHealthCheckIntervalSeconds(int $healthCheckIntervalSeconds): self
    {
        $this->healthCheckIntervalSeconds = $healthCheckIntervalSeconds;
        return $this;
    }

    public function getHealthCheckTimeoutSeconds(): int
    {
        return $this->healthCheckTimeoutSeconds;
    }

    public function setHealthCheckTimeoutSeconds(int $healthCheckTimeoutSeconds): self
    {
        $this->healthCheckTimeoutSeconds = $healthCheckTimeoutSeconds;
        return $this;
    }

    public function getHealthyThreshold(): int
    {
        return $this->healthyThreshold;
    }

    public function setHealthyThreshold(int $healthyThreshold): self
    {
        $this->healthyThreshold = $healthyThreshold;
        return $this;
    }

    public function getUnhealthyThreshold(): int
    {
        return $this->unhealthyThreshold;
    }

    public function setUnhealthyThreshold(int $unhealthyThreshold): self
    {
        $this->unhealthyThreshold = $unhealthyThreshold;
        return $this;
    }

    public function getStatus(): LoadBalancerStatusEnum
    {
        return $this->status;
    }

    public function setStatus(LoadBalancerStatusEnum $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function isTlsPolicyEnabled(): bool
    {
        return $this->tlsPolicyEnabled;
    }

    public function setTlsPolicyEnabled(bool $tlsPolicyEnabled): self
    {
        $this->tlsPolicyEnabled = $tlsPolicyEnabled;
        return $this;
    }

    public function getTlsCertificateName(): ?string
    {
        return $this->tlsCertificateName;
    }

    public function setTlsCertificateName(?string $tlsCertificateName): self
    {
        $this->tlsCertificateName = $tlsCertificateName;
        return $this;
    }

    public function getInstanceHealthSummary(): ?array
    {
        return $this->instanceHealthSummary;
    }

    public function setInstanceHealthSummary(?array $instanceHealthSummary): self
    {
        $this->instanceHealthSummary = $instanceHealthSummary;
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

    public function isConfigurationOptions(): bool
    {
        return $this->configurationOptions;
    }

    public function setConfigurationOptions(bool $configurationOptions): self
    {
        $this->configurationOptions = $configurationOptions;
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

    public function getAttachedInstances(): array
    {
        return $this->attachedInstances;
    }

    public function setAttachedInstances(array $attachedInstances): self
    {
        $this->attachedInstances = $attachedInstances;
        return $this;
    }

    public function addAttachedInstance(string $instanceName): self
    {
        if (!in_array($instanceName, $this->attachedInstances)) {
            $this->attachedInstances[] = $instanceName;
        }
        return $this;
    }

    public function removeAttachedInstance(string $instanceName): self
    {
        $index = array_search($instanceName, $this->attachedInstances);
        if ($index !== false) {
            unset($this->attachedInstances[$index]);
            $this->attachedInstances = array_values($this->attachedInstances);
        }
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
