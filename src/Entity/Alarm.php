<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Entity;

use AwsLightsailBundle\Enum\AlarmMetricEnum;
use AwsLightsailBundle\Enum\AlarmStateEnum;
use AwsLightsailBundle\Repository\AlarmRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: AlarmRepository::class)]
#[ORM\Table(name: 'aws_lightsail_alarm', options: ['comment' => 'AWS Lightsail 告警表'])]
class Alarm implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '主键ID'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '告警名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => 'AWS ARN'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $arn;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '关联资源名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $resourceName;

    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => '资源类型'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $resourceType;

    #[ORM\Column(type: Types::STRING, length: 100, enumType: AlarmMetricEnum::class, options: ['comment' => '指标名称'])]
    #[Assert\Choice(callback: [AlarmMetricEnum::class, 'cases'])]
    private AlarmMetricEnum $metricName;

    #[ORM\Column(type: Types::STRING, length: 50, enumType: AlarmStateEnum::class, options: ['comment' => '告警状态'])]
    #[Assert\Choice(callback: [AlarmStateEnum::class, 'cases'])]
    private AlarmStateEnum $state;

    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => 'AWS 区域'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $region;

    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => '比较运算符'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $comparisonOperator;

    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => '评估周期'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $evaluationPeriods;

    #[ORM\Column(type: Types::FLOAT, options: ['comment' => '阈值'])]
    #[Assert\NotBlank]
    private float $threshold;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '缺失数据处理方式'])]
    #[Assert\Length(max: 255)]
    private ?string $treatMissingData = null;

    /**
     * @var array<int, string>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '通知协议'])]
    #[Assert\Type(type: 'array')]
    private ?array $contactProtocols = null;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '监控资源信息'])]
    #[Assert\Type(type: 'array')]
    private ?array $monitoredResourceInfo = null;

    /**
     * @var array<int, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '触发告警需要的数据点'])]
    #[Assert\Type(type: 'array')]
    private ?array $datapointsToAlarm = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否启用通知'])]
    #[Assert\NotBlank]
    private bool $notificationEnabled = true;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '通知触发时间'])]
    #[Assert\Type(type: \DateTimeImmutable::class)]
    private ?\DateTimeImmutable $notificationTriggeredTime = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '同步时间'])]
    #[Assert\Type(type: \DateTimeImmutable::class)]
    private ?\DateTimeImmutable $syncTime = null;

    #[ORM\ManyToOne(targetEntity: AwsCredential::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private AwsCredential $credential;

    public function __construct()
    {
        $this->state = AlarmStateEnum::UNKNOWN;
    }

    public function __toString(): string
    {
        return \sprintf('Alarm %s (%s) for %s', $this->name, $this->state->value, $this->resourceName);
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

    public function getResourceName(): string
    {
        return $this->resourceName;
    }

    public function setResourceName(string $resourceName): void
    {
        $this->resourceName = $resourceName;
    }

    public function getResourceType(): string
    {
        return $this->resourceType;
    }

    public function setResourceType(string $resourceType): void
    {
        $this->resourceType = $resourceType;
    }

    public function getMetricName(): AlarmMetricEnum
    {
        return $this->metricName;
    }

    public function setMetricName(AlarmMetricEnum $metricName): void
    {
        $this->metricName = $metricName;
    }

    public function getState(): AlarmStateEnum
    {
        return $this->state;
    }

    public function setState(AlarmStateEnum $state): void
    {
        $this->state = $state;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function setRegion(string $region): void
    {
        $this->region = $region;
    }

    public function getComparisonOperator(): string
    {
        return $this->comparisonOperator;
    }

    public function setComparisonOperator(string $comparisonOperator): void
    {
        $this->comparisonOperator = $comparisonOperator;
    }

    public function getEvaluationPeriods(): string
    {
        return $this->evaluationPeriods;
    }

    public function setEvaluationPeriods(string $evaluationPeriods): void
    {
        $this->evaluationPeriods = $evaluationPeriods;
    }

    public function getThreshold(): float
    {
        return $this->threshold;
    }

    public function setThreshold(float $threshold): void
    {
        $this->threshold = $threshold;
    }

    public function getTreatMissingData(): ?string
    {
        return $this->treatMissingData;
    }

    public function setTreatMissingData(?string $treatMissingData): void
    {
        $this->treatMissingData = $treatMissingData;
    }

    /**
     * @return array<int, string>|null
     */
    public function getContactProtocols(): ?array
    {
        return $this->contactProtocols;
    }

    /**
     * @param array<int, string>|null $contactProtocols
     */
    public function setContactProtocols(?array $contactProtocols): void
    {
        $this->contactProtocols = $contactProtocols;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getMonitoredResourceInfo(): ?array
    {
        return $this->monitoredResourceInfo;
    }

    /**
     * @param array<string, mixed>|null $monitoredResourceInfo
     */
    public function setMonitoredResourceInfo(?array $monitoredResourceInfo): void
    {
        $this->monitoredResourceInfo = $monitoredResourceInfo;
    }

    /**
     * @return array<int, mixed>|null
     */
    public function getDatapointsToAlarm(): ?array
    {
        return $this->datapointsToAlarm;
    }

    /**
     * @param array<int, mixed>|null $datapointsToAlarm
     */
    public function setDatapointsToAlarm(?array $datapointsToAlarm): void
    {
        $this->datapointsToAlarm = $datapointsToAlarm;
    }

    public function isNotificationEnabled(): bool
    {
        return $this->notificationEnabled;
    }

    public function setNotificationEnabled(bool $notificationEnabled): void
    {
        $this->notificationEnabled = $notificationEnabled;
    }

    public function getNotificationTriggeredTime(): ?\DateTimeImmutable
    {
        return $this->notificationTriggeredTime;
    }

    public function setNotificationTriggeredTime(?\DateTimeInterface $notificationTriggeredTime): void
    {
        if (null !== $notificationTriggeredTime && !$notificationTriggeredTime instanceof \DateTimeImmutable) {
            $notificationTriggeredTime = \DateTimeImmutable::createFromInterface($notificationTriggeredTime);
        }
        $this->notificationTriggeredTime = $notificationTriggeredTime;
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
}
