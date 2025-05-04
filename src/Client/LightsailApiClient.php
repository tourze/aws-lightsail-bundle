<?php

namespace AwsLightsailBundle\Client;

use AwsLightsailBundle\Request\LightsailRequest;
use AwsLightsailBundle\Signature\AwsSignatureV4;
use HttpClientBundle\Client\ApiClient;
use HttpClientBundle\Request\RequestInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * AWS Lightsail API 客户端实现
 */
class LightsailApiClient extends ApiClient
{
    public function getBaseUrl(): string
    {
        return 'https://lightsail.amazonaws.com';
    }

    public function getLabel(): string
    {
        return 'AWS Lightsail API';
    }

    /**
     * 生成请求URL
     */
    protected function getRequestUrl(RequestInterface $request): string
    {
        if (!$request instanceof LightsailRequest) {
            throw new \InvalidArgumentException('请求必须是LightsailRequest类型');
        }

        $region = $request->getRegion();
        $baseUrl = "https://lightsail.$region.amazonaws.com";
        return $baseUrl . $request->getRequestPath();
    }

    /**
     * 获取请求方法
     */
    protected function getRequestMethod(RequestInterface $request): string
    {
        return $request->getRequestMethod() ?? 'POST';
    }

    /**
     * 获取请求选项
     */
    protected function getRequestOptions(RequestInterface $request): ?array
    {
        if (!$request instanceof LightsailRequest) {
            throw new \InvalidArgumentException('请求必须是LightsailRequest类型');
        }

        $options = $request->getRequestOptions() ?? [];
        $method = $this->getRequestMethod($request);
        $uri = $request->getRequestPath();

        // 准备请求体
        $payload = '';
        $headers = [
            'Content-Type' => 'application/x-amz-json-1.1',
            'Accept' => 'application/json',
        ];

        // 如果是POST请求，设置请求体
        if ($method === 'POST' && isset($options['json'])) {
            $payload = json_encode($options['json']);
            $headers['Content-Length'] = strlen($payload);
        }

        // 从请求中获取凭证创建签名
        $signer = new AwsSignatureV4(
            $request->getAccessKey(),
            $request->getSecretKey(),
            $request->getRegion()
        );

        // 签名请求
        $headers = $signer->signRequest(
            $method,
            $uri,
            [],
            $headers,
            $payload
        );

        $result = [
            'headers' => $headers,
        ];

        // 如果有请求体，添加到选项中
        if (!empty($payload)) {
            $result['body'] = $payload;
        }

        return $result;
    }

    /**
     * 格式化响应
     */
    protected function formatResponse(RequestInterface $request, ResponseInterface $response): mixed
    {
        return json_decode($response->getContent(), true);
    }
}
