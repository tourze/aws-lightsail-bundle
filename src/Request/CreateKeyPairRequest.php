<?php

namespace AwsLightsailBundle\Request;

/**
 * 创建Lightsail密钥对的请求
 * @see https://docs.aws.amazon.com/lightsail/2016-11-28/api-reference/API_CreateKeyPair.html
 */
class CreateKeyPairRequest extends LightsailRequest
{
    /**
     * @param string $keyPairName 密钥对名称
     * @param array $tags 标签
     */
    public function __construct(
        private readonly string $keyPairName,
        private readonly array $tags = []
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
        $data = [
            'keyPairName' => $this->keyPairName
        ];

        if (!empty($this->tags)) {
            $tags = [];
            foreach ($this->tags as $key => $value) {
                $tags[] = [
                    'key' => $key,
                    'value' => $value
                ];
            }
            $data['tags'] = $tags;
        }

        return [
            'json' => $data,
            'headers' => [
                'X-Amz-Target' => 'Lightsail_20161128.CreateKeyPair'
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
