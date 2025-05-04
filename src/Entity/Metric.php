<?php

namespace AwsLightsailBundle\Entity;

/**
 * AWS Lightsail 指标实体
 */
class Metric implements \Stringable
{
    public function __construct(
        private readonly string $metricName,
        private readonly string $resourceId,
        private readonly string $startTime,
        private readonly string $endTime,
        private readonly string $period,
        private readonly string $unit,
        private readonly array $datapoints = [],
        private readonly array $statistics = [],
    ) {
    }

    public function getMetricName(): string
    {
        return $this->metricName;
    }

    public function getResourceId(): string
    {
        return $this->resourceId;
    }

    public function getStartTime(): string
    {
        return $this->startTime;
    }

    public function getEndTime(): string
    {
        return $this->endTime;
    }

    public function getPeriod(): string
    {
        return $this->period;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function getDatapoints(): array
    {
        return $this->datapoints;
    }

    public function getStatistics(): array
    {
        return $this->statistics;
    }

    /**
     * 从API响应数据创建指标
     */
    public static function fromApiResponse(array $data): self
    {
        $metric = $data['metricData'] ?? $data;

        return new self(
            $metric['metricName'] ?? '',
            $metric['resourceId'] ?? '',
            $metric['startTime'] ?? '',
            $metric['endTime'] ?? '',
            $metric['period'] ?? '',
            $metric['unit'] ?? '',
            $metric['datapoints'] ?? [],
            $metric['statistics'] ?? [],
        );
    }

    /**
     * 返回指标的字符串表示
     */
    public function __toString(): string
    {
        return sprintf('%s (%s)', $this->metricName, $this->resourceId);
    }
}
