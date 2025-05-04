<?php

namespace AwsLightsailBundle\Request;

/**
 * 打开Lightsail实例公共端口的请求
 * @see https://docs.aws.amazon.com/lightsail/2016-11-28/api-reference/API_OpenInstancePublicPorts.html
 */
class OpenInstancePublicPortsRequest extends LightsailRequest
{
    /**
     * @param string $instanceName 实例名称
     * @param int $fromPort 起始端口
     * @param int $toPort 结束端口
     * @param string $protocol 协议
     * @param array $cidrs CIDR列表
     * @param array $ipv6Cidrs IPv6 CIDR列表
     * @param array $cidrListAliases CIDR列表别名
     */
    public function __construct(
        private readonly string $instanceName,
        private readonly int $fromPort,
        private readonly int $toPort,
        private readonly string $protocol,
        private readonly array $cidrs = [],
        private readonly array $ipv6Cidrs = [],
        private readonly array $cidrListAliases = []
    ) {
    }

    /**
     * API 端点
     */
    public function getRequestPath(): string
    {
        return '/';
    }

    /**
     * 请求参数
     */
    public function getRequestOptions(): ?array
    {
        return [
            'json' => [
                'instanceName' => $this->instanceName,
                'portInfo' => [
                    'fromPort' => $this->fromPort,
                    'toPort' => $this->toPort,
                    'protocol' => $this->protocol,
                    'cidrs' => $this->cidrs,
                    'ipv6Cidrs' => $this->ipv6Cidrs,
                    'cidrListAliases' => $this->cidrListAliases,
                ]
            ],
            'headers' => [
                'X-Amz-Target' => 'Lightsail_20161128.OpenInstancePublicPorts'
            ]
        ];
    }

    /**
     * 请求方法
     */
    public function getRequestMethod(): ?string
    {
        return 'POST';
    }
}
