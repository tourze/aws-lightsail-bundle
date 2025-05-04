<?php

namespace AwsLightsailBundle\Entity;

/**
 * AWS Lightsail 网络实体
 */
class Network implements \Stringable
{
    public function __construct(
        private readonly string $instanceName,
        private readonly array $ports = [],
        private readonly array $ipv6Addresses = [],
        private readonly array $ipv4Addresses = [],
        private readonly array $protocols = [],
    ) {
    }

    public function getInstanceName(): string
    {
        return $this->instanceName;
    }

    public function getPorts(): array
    {
        return $this->ports;
    }

    public function getIpv6Addresses(): array
    {
        return $this->ipv6Addresses;
    }

    public function getIpv4Addresses(): array
    {
        return $this->ipv4Addresses;
    }

    public function getProtocols(): array
    {
        return $this->protocols;
    }

    /**
     * 从API响应数据创建网络配置
     */
    public static function fromApiResponse(array $data): self
    {
        $network = $data['network'] ?? $data;

        return new self(
            $network['instanceName'] ?? '',
            $network['ports'] ?? [],
            $network['ipv6Addresses'] ?? [],
            $network['ipv4Addresses'] ?? [],
            $network['protocols'] ?? [],
        );
    }

    /**
     * 返回网络配置的字符串表示
     */
    public function __toString(): string
    {
        return $this->instanceName;
    }
}
