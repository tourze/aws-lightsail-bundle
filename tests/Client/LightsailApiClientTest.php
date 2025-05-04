<?php

namespace AwsLightsailBundle\Tests\Client;

use AwsLightsailBundle\Client\LightsailApiClient;
use AwsLightsailBundle\Request\LightsailRequest;
use HttpClientBundle\Request\RequestInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\ResponseInterface;

class LightsailApiClientTest extends TestCase
{
    /**
     * 测试基本URL
     */
    public function testGetBaseUrl()
    {
        $client = new LightsailApiClient();
        $this->assertEquals('https://lightsail.amazonaws.com', $client->getBaseUrl());
    }

    /**
     * 测试标签
     */
    public function testGetLabel()
    {
        $client = new LightsailApiClient();
        $this->assertEquals('AWS Lightsail API', $client->getLabel());
    }

    /**
     * 测试获取请求URL
     */
    public function testGetRequestUrl()
    {
        $client = new LightsailApiClient();

        // 创建模拟的请求对象
        $request = $this->createMock(LightsailRequest::class);
        $request->method('getRegion')->willReturn('us-west-2');
        $request->method('getRequestPath')->willReturn('/test-path');

        // 使用反射调用受保护的方法
        $method = new \ReflectionMethod(LightsailApiClient::class, 'getRequestUrl');
        $method->setAccessible(true);

        $url = $method->invoke($client, $request);
        $this->assertEquals('https://lightsail.us-west-2.amazonaws.com/test-path', $url);
    }

    /**
     * 测试获取请求URL - 非法请求类型
     */
    public function testGetRequestUrl_withInvalidRequestType()
    {
        $client = new LightsailApiClient();

        // 创建一个非LightsailRequest类型的请求
        $request = $this->createMock(RequestInterface::class);

        // 使用反射调用受保护的方法
        $method = new \ReflectionMethod(LightsailApiClient::class, 'getRequestUrl');
        $method->setAccessible(true);

        $this->expectException(\InvalidArgumentException::class);
        $method->invoke($client, $request);
    }

    /**
     * 测试获取请求方法
     */
    public function testGetRequestMethod()
    {
        $client = new LightsailApiClient();

        // 创建模拟的请求对象
        $request = $this->createMock(LightsailRequest::class);
        $request->method('getRequestMethod')->willReturn('GET');

        // 使用反射调用受保护的方法
        $method = new \ReflectionMethod(LightsailApiClient::class, 'getRequestMethod');
        $method->setAccessible(true);

        $requestMethod = $method->invoke($client, $request);
        $this->assertEquals('GET', $requestMethod);
    }

    /**
     * 测试获取请求选项 - GET请求
     */
    public function testGetRequestOptions_withGetRequest()
    {
        $client = new LightsailApiClient();

        // 创建模拟的请求对象
        $request = $this->createMock(LightsailRequest::class);
        $request->method('getRequestMethod')->willReturn('GET');
        $request->method('getRequestPath')->willReturn('/test');
        $request->method('getAccessKey')->willReturn('test-access');
        $request->method('getSecretKey')->willReturn('test-secret');
        $request->method('getRegion')->willReturn('us-east-1');
        $request->method('getRequestOptions')->willReturn(['query' => ['param' => 'value']]);

        // 使用反射调用受保护的方法
        $method = new \ReflectionMethod(LightsailApiClient::class, 'getRequestOptions');
        $method->setAccessible(true);

        $options = $method->invoke($client, $request);

        $this->assertIsArray($options);
        $this->assertArrayHasKey('headers', $options);
        $this->assertArrayHasKey('Content-Type', $options['headers']);
        $this->assertArrayHasKey('Accept', $options['headers']);
        $this->assertArrayHasKey('Authorization', $options['headers']);
    }

    /**
     * 测试获取请求选项 - POST请求
     */
    public function testGetRequestOptions_withPostRequest()
    {
        $client = new LightsailApiClient();

        // 创建模拟的请求对象
        $request = $this->createMock(LightsailRequest::class);
        $request->method('getRequestMethod')->willReturn('POST');
        $request->method('getRequestPath')->willReturn('/test');
        $request->method('getAccessKey')->willReturn('test-access');
        $request->method('getSecretKey')->willReturn('test-secret');
        $request->method('getRegion')->willReturn('us-east-1');
        $request->method('getRequestOptions')->willReturn(['json' => ['param' => 'value']]);

        // 使用反射调用受保护的方法
        $method = new \ReflectionMethod(LightsailApiClient::class, 'getRequestOptions');
        $method->setAccessible(true);

        $options = $method->invoke($client, $request);

        $this->assertIsArray($options);
        $this->assertArrayHasKey('headers', $options);
        $this->assertArrayHasKey('Content-Type', $options['headers']);
        $this->assertArrayHasKey('Accept', $options['headers']);
        $this->assertArrayHasKey('Authorization', $options['headers']);
        $this->assertArrayHasKey('body', $options);
    }

    /**
     * 测试格式化响应
     */
    public function testFormatResponse()
    {
        $client = new LightsailApiClient();

        // 创建模拟的请求对象
        $request = $this->createMock(LightsailRequest::class);

        // 创建模拟的响应对象
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getContent')->willReturn('{"key":"value"}');

        // 使用反射调用受保护的方法
        $method = new \ReflectionMethod(LightsailApiClient::class, 'formatResponse');
        $method->setAccessible(true);

        $result = $method->invoke($client, $request, $response);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('key', $result);
        $this->assertEquals('value', $result['key']);
    }

    /**
     * 测试获取请求选项 - 非法请求类型
     */
    public function testGetRequestOptions_withInvalidRequestType()
    {
        $client = new LightsailApiClient();

        // 创建一个非LightsailRequest类型的请求
        $request = $this->createMock(RequestInterface::class);

        // 使用反射调用受保护的方法
        $method = new \ReflectionMethod(LightsailApiClient::class, 'getRequestOptions');
        $method->setAccessible(true);

        $this->expectException(\InvalidArgumentException::class);
        $method->invoke($client, $request);
    }
}
