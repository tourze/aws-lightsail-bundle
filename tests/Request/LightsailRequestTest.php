<?php

namespace AwsLightsailBundle\Tests\Request;

use AwsLightsailBundle\Request\LightsailRequest;
use PHPUnit\Framework\TestCase;

class LightsailRequestTest extends TestCase
{
    /**
     * 创建一个具体的LightsailRequest实现用于测试
     */
    private function createRequest(): LightsailRequest
    {
        return new class extends LightsailRequest {
            /**
             * 自定义请求路径
             */
            public function getRequestPath(): string
            {
                return '/custom-path';
            }

            /**
             * 自定义请求选项
             */
            public function getRequestOptions(): ?array
            {
                return [
                    'json' => ['testKey' => 'testValue'],
                    'headers' => [
                        'X-Custom-Header' => 'CustomValue'
                    ]
                ];
            }

            /**
             * 自定义请求方法
             */
            public function getRequestMethod(): ?string
            {
                return 'GET';
            }
        };
    }

    /**
     * 测试凭证设置
     */
    public function testSetCredentials()
    {
        $request = $this->createRequest();
        $request->setCredentials('test-access', 'test-secret', 'us-west-2');

        $this->assertEquals('test-access', $request->getAccessKey());
        $this->assertEquals('test-secret', $request->getSecretKey());
        $this->assertEquals('us-west-2', $request->getRegion());
    }

    /**
     * 测试默认凭证值
     */
    public function testDefaultCredentials()
    {
        $request = $this->createRequest();

        $this->assertEquals('', $request->getAccessKey());
        $this->assertEquals('', $request->getSecretKey());
        $this->assertEquals('us-east-1', $request->getRegion());
    }

    /**
     * 测试链式调用
     */
    public function testChaining()
    {
        $request = $this->createRequest();
        $result = $request->setCredentials('test-access', 'test-secret', 'us-west-2');

        $this->assertSame($request, $result);
    }

    /**
     * 测试覆盖默认方法
     */
    public function testOverriddenMethods()
    {
        $request = $this->createRequest();

        $this->assertEquals('/custom-path', $request->getRequestPath());
        $this->assertEquals('GET', $request->getRequestMethod());

        $options = $request->getRequestOptions();
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertEquals('testValue', $options['json']['testKey']);
        $this->assertArrayHasKey('headers', $options);
        $this->assertEquals('CustomValue', $options['headers']['X-Custom-Header']);
    }

    /**
     * 测试凭证设置 - 空值
     */
    public function testSetCredentials_withEmptyValues()
    {
        $request = $this->createRequest();
        $request->setCredentials('', '', '');

        $this->assertEquals('', $request->getAccessKey());
        $this->assertEquals('', $request->getSecretKey());
        $this->assertEquals('', $request->getRegion());
    }

    /**
     * 测试凭证设置 - 多次调用
     */
    public function testSetCredentials_multipleCalls()
    {
        $request = $this->createRequest();

        $request->setCredentials('access1', 'secret1', 'region1');
        $this->assertEquals('access1', $request->getAccessKey());
        $this->assertEquals('secret1', $request->getSecretKey());
        $this->assertEquals('region1', $request->getRegion());

        $request->setCredentials('access2', 'secret2', 'region2');
        $this->assertEquals('access2', $request->getAccessKey());
        $this->assertEquals('secret2', $request->getSecretKey());
        $this->assertEquals('region2', $request->getRegion());
    }
}
