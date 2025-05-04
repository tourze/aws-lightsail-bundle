<?php

namespace AwsLightsailBundle\Tests\Signature;

use AwsLightsailBundle\Signature\AwsSignatureV4;
use PHPUnit\Framework\TestCase;

class AwsSignatureV4Test extends TestCase
{
    /**
     * 测试构造函数参数设置
     */
    public function testConstructor()
    {
        $signer = new AwsSignatureV4('test-key', 'test-secret', 'us-east-1');

        // 使用反射检查私有属性
        $reflection = new \ReflectionClass($signer);

        $accessKeyProperty = $reflection->getProperty('accessKey');
        $accessKeyProperty->setAccessible(true);
        $this->assertEquals('test-key', $accessKeyProperty->getValue($signer));

        $secretKeyProperty = $reflection->getProperty('secretKey');
        $secretKeyProperty->setAccessible(true);
        $this->assertEquals('test-secret', $secretKeyProperty->getValue($signer));

        $regionProperty = $reflection->getProperty('region');
        $regionProperty->setAccessible(true);
        $this->assertEquals('us-east-1', $regionProperty->getValue($signer));

        $serviceProperty = $reflection->getProperty('service');
        $serviceProperty->setAccessible(true);
        $this->assertEquals('lightsail', $serviceProperty->getValue($signer));
    }

    /**
     * 测试签名生成 - 基本参数
     */
    public function testSignRequest_withBasicParameters()
    {
        $signer = new AwsSignatureV4('test-key', 'test-secret', 'us-east-1');
        $headers = $signer->signRequest(
            'POST',
            '/',
            [],
            ['Content-Type' => 'application/json'],
            '{"test":"value"}'
        );

        $this->assertArrayHasKey('Authorization', $headers);
        $this->assertArrayHasKey('x-amz-date', $headers);
        $this->assertArrayHasKey('x-amz-content-sha256', $headers);
        $this->assertStringContainsString('AWS4-HMAC-SHA256', $headers['Authorization']);
        $this->assertStringContainsString('Credential=test-key', $headers['Authorization']);
        $this->assertStringContainsString('us-east-1', $headers['Authorization']);
        $this->assertStringContainsString('lightsail', $headers['Authorization']);

        // 验证日期格式
        $this->assertMatchesRegularExpression('/^\d{8}T\d{6}Z$/', $headers['x-amz-date']);

        // 验证内容哈希
        $expectedContentHash = hash('sha256', '{"test":"value"}');
        $this->assertEquals($expectedContentHash, $headers['x-amz-content-sha256']);
    }

    /**
     * 测试签名生成 - 带查询参数
     */
    public function testSignRequest_withQueryParameters()
    {
        $signer = new AwsSignatureV4('test-key', 'test-secret', 'us-east-1');
        $headers = $signer->signRequest(
            'GET',
            '/',
            ['Action' => 'ListInstances', 'Version' => '2016-11-28'],
            [],
            ''
        );

        $this->assertArrayHasKey('Authorization', $headers);
        $this->assertStringContainsString('AWS4-HMAC-SHA256', $headers['Authorization']);
    }

    /**
     * 测试签名生成 - 不同请求方法
     */
    public function testSignRequest_withDifferentMethods()
    {
        $signer = new AwsSignatureV4('test-key', 'test-secret', 'us-east-1');

        // GET 请求
        $headersGet = $signer->signRequest(
            'GET',
            '/',
            [],
            [],
            ''
        );
        $this->assertArrayHasKey('Authorization', $headersGet);

        // POST 请求
        $headersPost = $signer->signRequest(
            'POST',
            '/',
            [],
            ['Content-Type' => 'application/json'],
            '{"test":"value"}'
        );
        $this->assertArrayHasKey('Authorization', $headersPost);

        // DELETE 请求
        $headersDelete = $signer->signRequest(
            'DELETE',
            '/',
            [],
            [],
            ''
        );
        $this->assertArrayHasKey('Authorization', $headersDelete);

        // 验证不同方法的签名不同
        $this->assertNotEquals($headersGet['Authorization'], $headersPost['Authorization']);
        $this->assertNotEquals($headersGet['Authorization'], $headersDelete['Authorization']);
        $this->assertNotEquals($headersPost['Authorization'], $headersDelete['Authorization']);
    }

    /**
     * 测试签名生成 - 空参数
     */
    public function testSignRequest_withEmptyParameters()
    {
        $signer = new AwsSignatureV4('test-key', 'test-secret', 'us-east-1');

        // 空URI
        $headersEmptyUri = $signer->signRequest(
            'POST',
            '',
            [],
            ['Content-Type' => 'application/json'],
            '{"test":"value"}'
        );
        $this->assertArrayHasKey('Authorization', $headersEmptyUri);

        // 空头部
        $headersEmptyHeaders = $signer->signRequest(
            'POST',
            '/',
            [],
            [],
            '{"test":"value"}'
        );
        $this->assertArrayHasKey('Authorization', $headersEmptyHeaders);
        $this->assertArrayHasKey('x-amz-date', $headersEmptyHeaders);

        // 空请求体
        $headersEmptyPayload = $signer->signRequest(
            'POST',
            '/',
            [],
            ['Content-Type' => 'application/json'],
            ''
        );
        $this->assertArrayHasKey('Authorization', $headersEmptyPayload);
        $this->assertEquals(hash('sha256', ''), $headersEmptyPayload['x-amz-content-sha256']);
    }

    /**
     * 测试签名生成 - 不同区域
     */
    public function testSignRequest_withDifferentRegions()
    {
        $signerUsEast = new AwsSignatureV4('test-key', 'test-secret', 'us-east-1');
        $signerUsWest = new AwsSignatureV4('test-key', 'test-secret', 'us-west-2');

        $headersUsEast = $signerUsEast->signRequest(
            'POST',
            '/',
            [],
            ['Content-Type' => 'application/json'],
            '{"test":"value"}'
        );

        $headersUsWest = $signerUsWest->signRequest(
            'POST',
            '/',
            [],
            ['Content-Type' => 'application/json'],
            '{"test":"value"}'
        );

        // 验证不同区域的签名不同
        $this->assertNotEquals($headersUsEast['Authorization'], $headersUsWest['Authorization']);
        $this->assertStringContainsString('us-east-1', $headersUsEast['Authorization']);
        $this->assertStringContainsString('us-west-2', $headersUsWest['Authorization']);

        // 验证主机头也不同
        $this->assertEquals('lightsail.us-east-1.amazonaws.com', $headersUsEast['host']);
        $this->assertEquals('lightsail.us-west-2.amazonaws.com', $headersUsWest['host']);
    }

    /**
     * 测试签名生成 - 不同服务
     */
    public function testSignRequest_withDifferentService()
    {
        $signerLightsail = new AwsSignatureV4('test-key', 'test-secret', 'us-east-1', 'lightsail');
        $signerEc2 = new AwsSignatureV4('test-key', 'test-secret', 'us-east-1', 'ec2');

        $headersLightsail = $signerLightsail->signRequest(
            'POST',
            '/',
            [],
            ['Content-Type' => 'application/json'],
            '{"test":"value"}'
        );

        $headersEc2 = $signerEc2->signRequest(
            'POST',
            '/',
            [],
            ['Content-Type' => 'application/json'],
            '{"test":"value"}'
        );

        // 验证不同服务的签名不同
        $this->assertNotEquals($headersLightsail['Authorization'], $headersEc2['Authorization']);
        $this->assertStringContainsString('lightsail', $headersLightsail['Authorization']);
        $this->assertStringContainsString('ec2', $headersEc2['Authorization']);
    }
}
