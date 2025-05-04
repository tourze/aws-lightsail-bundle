<?php

namespace AwsLightsailBundle\Signature;

/**
 * AWS 签名生成工具
 */
class AwsSignatureV4
{
    public function __construct(
        private readonly string $accessKey,
        private readonly string $secretKey,
        private readonly string $region,
        private readonly string $service = 'lightsail'
    ) {
    }

    /**
     * 生成请求签名
     */
    public function signRequest(
        string $method,
        string $uri,
        array $queryParams = [],
        array $headers = [],
        string $payload = ''
    ): array {
        $datetime = gmdate('Ymd\THis\Z');
        $date = substr($datetime, 0, 8);

        // 标准请求
        $headers['host'] = "lightsail.$this->region.amazonaws.com";
        $headers['x-amz-date'] = $datetime;
        $canonicalHeaders = $this->getCanonicalHeaders($headers);
        $signedHeaders = implode(';', array_keys($headers));

        // 构建规范请求
        $canonicalRequest = implode("\n", [
            $method,
            $uri,
            $this->getCanonicalQueryString($queryParams),
            $canonicalHeaders,
            $signedHeaders,
            hash('sha256', $payload)
        ]);

        // 凭证范围
        $credentialScope = "$date/$this->region/$this->service/aws4_request";

        // 字符串签名
        $stringToSign = implode("\n", [
            'AWS4-HMAC-SHA256',
            $datetime,
            $credentialScope,
            hash('sha256', $canonicalRequest)
        ]);

        // 签名计算
        $kSecret = "AWS4{$this->secretKey}";
        $kDate = hash_hmac('sha256', $date, $kSecret, true);
        $kRegion = hash_hmac('sha256', $this->region, $kDate, true);
        $kService = hash_hmac('sha256', $this->service, $kRegion, true);
        $kSigning = hash_hmac('sha256', 'aws4_request', $kService, true);

        $signature = hash_hmac('sha256', $stringToSign, $kSigning);

        // 构建认证头
        $authHeader = "AWS4-HMAC-SHA256 "
            . "Credential={$this->accessKey}/$credentialScope, "
            . "SignedHeaders=$signedHeaders, "
            . "Signature=$signature";

        $headers['Authorization'] = $authHeader;
        $headers['x-amz-content-sha256'] = hash('sha256', $payload);

        return $headers;
    }

    /**
     * 获取规范化的查询字符串
     */
    private function getCanonicalQueryString(array $queryParams): string
    {
        if (empty($queryParams)) {
            return '';
        }

        ksort($queryParams);
        $canonicalQueryStrings = [];

        foreach ($queryParams as $key => $value) {
            $canonicalQueryStrings[] = rawurlencode($key) . '=' . rawurlencode($value);
        }

        return implode('&', $canonicalQueryStrings);
    }

    /**
     * 获取规范化的请求头
     */
    private function getCanonicalHeaders(array $headers): string
    {
        $canonicalHeaders = [];

        // 规范化头部名称并排序
        $headersToSign = [];
        foreach ($headers as $name => $value) {
            $name = strtolower(trim($name));
            $value = trim($value);
            $headersToSign[$name] = $value;
        }

        ksort($headersToSign);

        foreach ($headersToSign as $name => $value) {
            $canonicalHeaders[] = $name . ':' . $value;
        }

        return implode("\n", $canonicalHeaders) . "\n";
    }
}
