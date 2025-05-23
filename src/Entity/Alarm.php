<?php

namespace AwsLightsailBundle\Entity;

use AwsLightsailBundle\Enum\AlarmMetricEnum;
use AwsLightsailBundle\Enum\AlarmStateEnum;
use AwsLightsailBundle\Repository\AlarmRepository;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;

#[ORM\Entity(repositoryClass: AlarmRepository::class)]
#[ORM\Table(name: 'aws_lightsail_alarm', options: ['comment' => 'AWS Lightsail 告警表'])]
class Alarm implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '告警名称'])]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => 'AWS ARN'])]
    private string $arn;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '关联资源名称'])]
    private string $resourceName;

    #[ORM\Column(type: 'string', length: 50, options: ['comment' => '资源类型'])]
    private string $resourceType;

    #[ORM\Column(type: 'string', length: 100, enumType: AlarmMetricEnum::class, options: ['comment' => '指标名称'])]
    private AlarmMetricEnum $metricName;

    #[ORM\Column(type: 'string', length: 50, enumType: AlarmStateEnum::class, options: ['comment' => '告警状态'])]
    private AlarmStateEnum $state;

    #[ORM\Column(type: 'string', length: 50, options: ['comment' => 'AWS 区域'])]
    private string $region;

    #[ORM\Column(type: 'string', length: 50, options: ['comment' => '比较运算符'])]
    private string $comparisonOperator;

    #[ORM\Column(type: 'string', length: 50, options: ['comment' => '评估周期'])]
    private string $evaluationPeriods;

    #[ORM\Column(type: 'float', options: ['comment' => '阈值'])]
    private float $threshold;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['comment' => '缺失数据处理方式'])]
    private ?string $treatMissingData = null;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => '通知协议'])]
    private ?array $contactProtocols = null;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => '监控资源信息'])]
    private ?array $monitoredResourceInfo = null;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => '触发告警需要的数据点'])]
    private ?array $datapointsToAlarm = null;

    #[ORM\Column(type: 'boolean', options: ['comment' => '是否启用通知'])]
    private bool $notificationEnabled = true;

    #[ORM\Column(type: 'datetime_immutable', nullable: true, options: ['comment' => '通知触发时间'])]
    private ?\DateTimeImmutable $notificationTriggeredTime = null;

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
        $this->state = AlarmStateEnum::UNKNOWN;
    }

    public function __toString(): string
    {
        return sprintf('Alarm %s (%s) for %s', $this->name, $this->state->value, $this->resourceName);
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

    public function getResourceName(): string
    {
        return $this->resourceName;
    }

    public function setResourceName(string $resourceName): self
    {
        $this->resourceName = $resourceName;
        return $this;
    }

    public function getResourceType(): string
    {
        return $this->resourceType;
    }

    public function setResourceType(string $resourceType): self
    {
        $this->resourceType = $resourceType;
        return $this;
    }

    public function getMetricName(): AlarmMetricEnum
    {
        return $this->metricName;
    }

    public function setMetricName(AlarmMetricEnum $metricName): self
    {
        $this->metricName = $metricName;
        return $this;
    }

    public function getState(): AlarmStateEnum
    {
        return $this->state;
    }

    public function setState(AlarmStateEnum $state): self
    {
        $this->state = $state;
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

    public function getComparisonOperator(): string
    {
        return $this->comparisonOperator;
    }

    public function setComparisonOperator(string $comparisonOperator): self
    {
        $this->comparisonOperator = $comparisonOperator;
        return $this;
    }

    public function getEvaluationPeriods(): string
    {
        return $this->evaluationPeriods;
    }

    public function setEvaluationPeriods(string $evaluationPeriods): self
    {
        $this->evaluationPeriods = $evaluationPeriods;
        return $this;
    }

    public function getThreshold(): float
    {
        return $this->threshold;
    }

    public function setThreshold(float $threshold): self
    {
        $this->threshold = $threshold;
        return $this;
    }

    public function getTreatMissingData(): ?string
    {
        return $this->treatMissingData;
    }

    public function setTreatMissingData(?string $treatMissingData): self
    {
        $this->treatMissingData = $treatMissingData;
        return $this;
    }

    public function getContactProtocols(): ?array
    {
        return $this->contactProtocols;
    }

    public function setContactProtocols(?array $contactProtocols): self
    {
        $this->contactProtocols = $contactProtocols;
        return $this;
    }

    public function getMonitoredResourceInfo(): ?array
    {
        return $this->monitoredResourceInfo;
    }

    public function setMonitoredResourceInfo(?array $monitoredResourceInfo): self
    {
        $this->monitoredResourceInfo = $monitoredResourceInfo;
        return $this;
    }

    public function getDatapointsToAlarm(): ?array
    {
        return $this->datapointsToAlarm;
    }

    public function setDatapointsToAlarm(?array $datapointsToAlarm): self
    {
        $this->datapointsToAlarm = $datapointsToAlarm;
        return $this;
    }

    public function isNotificationEnabled(): bool
    {
        return $this->notificationEnabled;
    }

    public function setNotificationEnabled(bool $notificationEnabled): self
    {
        $this->notificationEnabled = $notificationEnabled;
        return $this;
    }

    public function getNotificationTriggeredTime(): ?\DateTimeImmutable
    {
        return $this->notificationTriggeredTime;
    }

    public function setNotificationTriggeredTime(?\DateTimeImmutable $notificationTriggeredTime): self
    {
        $this->notificationTriggeredTime = $notificationTriggeredTime;
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