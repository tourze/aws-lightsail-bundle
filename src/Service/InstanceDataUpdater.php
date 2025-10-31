<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Service;

use AwsLightsailBundle\Entity\Instance;
use Carbon\CarbonImmutable;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;

/**
 * 实例数据更新辅助类
 */
#[WithMonologChannel(channel: 'aws_lightsail')]
readonly class InstanceDataUpdater
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    /**
     * 更新硬件和配置字段
     *
     * @param Instance                    $instance
     * @param array<string, mixed>        $data     AWS API 返回的实例数据
     */
    public function updateHardwareAndConfigFields(Instance $instance, array $data): void
    {
        // 硬件配置
        if (isset($data['hardware']) && \is_array($data['hardware'])) {
            /** @var array<string, mixed> $hardware */
            $hardware = $data['hardware'];
            $instance->setHardware($hardware);
        }

        // 元数据选项
        if (isset($data['metadataOptions']) && \is_array($data['metadataOptions'])) {
            /** @var array<string, mixed> $metadataOptions */
            $metadataOptions = $data['metadataOptions'];
            $instance->setMetadataOptions($metadataOptions);
        }

        // 标签
        $this->updateInstanceTags($instance, $data);

        // 监控状态
        if (isset($data['isMonitored'])) {
            $instance->setIsMonitoring((bool) $data['isMonitored']);
        }
    }

    /**
     * 更新实例标签
     *
     * @param Instance                    $instance
     * @param array<string, mixed>        $data     AWS API 返回的实例数据
     */
    private function updateInstanceTags(Instance $instance, array $data): void
    {
        if (isset($data['tags']) && \is_array($data['tags'])) {
            /** @var array<string, mixed> $tags */
            $tags = [];
            foreach ($data['tags'] as $tag) {
                if (\is_array($tag) && isset($tag['key'], $tag['value']) && \is_string($tag['key'])) {
                    $tags[$tag['key']] = $tag['value'];
                }
            }
            $instance->setTags($tags);
        }
    }

    /**
     * 更新时间戳字段
     *
     * @param Instance                    $instance
     * @param array<string, mixed>        $data     AWS API 返回的实例数据
     */
    public function updateTimestampFields(Instance $instance, array $data): void
    {
        if (!isset($data['createdAt'])) {
            return;
        }

        try {
            $createdAt = $this->parseDateTime($data['createdAt']);
            if (null !== $createdAt) {
                $instance->setAwsCreationTime($createdAt);
            }
        } catch (\Throwable $e) {
            $this->logger->warning('无法解析 AWS 创建时间', [
                'createdAt' => $data['createdAt'],
                'error'     => $e->getMessage(),
            ]);
        }
    }

    /**
     * 解析日期时间
     *
     * @param mixed $dateTime
     *
     * @return \DateTimeImmutable|null
     */
    private function parseDateTime($dateTime): ?\DateTimeImmutable
    {
        if ($dateTime instanceof \DateTimeImmutable) {
            return $dateTime;
        }

        if ($dateTime instanceof \DateTime) {
            return CarbonImmutable::parse($dateTime);
        }

        if (\is_string($dateTime)) {
            return CarbonImmutable::parse($dateTime);
        }

        if (\is_int($dateTime) || \is_float($dateTime)) {
            return CarbonImmutable::parse((string) $dateTime);
        }

        if (\is_object($dateTime) && \method_exists($dateTime, 'format')) {
            // 处理 AWS SDK 的 DateTimeResult 对象
            $formatted = $dateTime->format('c');
            if (\is_string($formatted)) {
                return CarbonImmutable::parse($formatted);
            }
        }

        return null;
    }

    /**
     * 更新网络字段
     *
     * @param Instance                    $instance
     * @param array<string, mixed>        $data     AWS API 返回的实例数据
     */
    public function updateNetworkFields(Instance $instance, array $data): void
    {
        $this->updateIpAddresses($instance, $data);
        $this->updateNetworkingConfig($instance, $data);
    }

    /**
     * 更新 IP 地址相关字段
     *
     * @param Instance                    $instance
     * @param array<string, mixed>        $data     AWS API 返回的实例数据
     */
    private function updateIpAddresses(Instance $instance, array $data): void
    {
        // 公网 IP 地址
        if (isset($data['publicIpAddress'])) {
            $publicIpAddress = $data['publicIpAddress'];
            $instance->setPublicIpAddress(\is_string($publicIpAddress) ? $publicIpAddress : null);
        }

        // 私网 IP 地址
        if (isset($data['privateIpAddress'])) {
            $privateIpAddress = $data['privateIpAddress'];
            $instance->setPrivateIpAddress(\is_string($privateIpAddress) ? $privateIpAddress : null);
        }

        // IPv6 地址
        $this->updateIpv6Addresses($instance, $data);

        // IP 地址类型
        if (isset($data['ipAddressType'])) {
            $ipAddressType = $data['ipAddressType'];
            $instance->setIpAddressType(\is_string($ipAddressType) ? $ipAddressType : null);
        }
    }

    /**
     * 更新 IPv6 地址
     *
     * @param Instance                    $instance
     * @param array<string, mixed>        $data     AWS API 返回的实例数据
     */
    private function updateIpv6Addresses(Instance $instance, array $data): void
    {
        if (!isset($data['ipv6Addresses']) || !\is_array($data['ipv6Addresses'])) {
            return;
        }

        /** @var array<int, string> $ipv6Addresses */
        $ipv6Addresses = [];
        foreach ($data['ipv6Addresses'] as $ipv6) {
            if (\is_string($ipv6)) {
                $ipv6Addresses[] = $ipv6;
            }
        }
        $instance->setIpv6Addresses($ipv6Addresses);
    }

    /**
     * 更新网络配置相关字段
     *
     * @param Instance                    $instance
     * @param array<string, mixed>        $data     AWS API 返回的实例数据
     */
    private function updateNetworkingConfig(Instance $instance, array $data): void
    {
        // 是否为静态 IP
        if (isset($data['isStaticIp'])) {
            $instance->setIsStaticIp((bool) $data['isStaticIp']);
        }

        // 网络配置
        if (isset($data['networking']) && \is_array($data['networking'])) {
            /** @var array<string, mixed> $networking */
            $networking = $data['networking'];
            $instance->setNetworking($networking);
        }
    }
}
