<?php

namespace AwsLightsailBundle\Tests\Request;

use AwsLightsailBundle\Request\CreateInstanceRequest;
use PHPUnit\Framework\TestCase;

class CreateInstanceRequestTest extends TestCase
{
    /**
     * 测试使用所有必需参数创建请求
     */
    public function testCreateInstanceRequest_withRequiredParameters()
    {
        $request = new CreateInstanceRequest(
            ['test-instance'],
            'us-east-1a',
            'amazon_linux_2',
            'nano_3_0',
            'test-key'
        );

        $this->assertEquals('/', $request->getRequestPath());
        $this->assertEquals('POST', $request->getRequestMethod());

        $options = $request->getRequestOptions();
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertEquals(['test-instance'], $options['json']['instanceNames']);
        $this->assertEquals('us-east-1a', $options['json']['availabilityZone']);
        $this->assertEquals('amazon_linux_2', $options['json']['blueprintId']);
        $this->assertEquals('nano_3_0', $options['json']['bundleId']);
        $this->assertEquals('test-key', $options['json']['keyPairName']);
        $this->assertEquals('ipv4', $options['json']['ipAddressType']);

        $this->assertArrayHasKey('headers', $options);
        $this->assertArrayHasKey('X-Amz-Target', $options['headers']);
        $this->assertEquals('Lightsail_20161128.CreateInstances', $options['headers']['X-Amz-Target']);
    }

    /**
     * 测试使用自定义IP地址类型
     */
    public function testCreateInstanceRequest_withCustomIpAddressType()
    {
        $request = new CreateInstanceRequest(
            ['test-instance'],
            'us-east-1a',
            'amazon_linux_2',
            'nano_3_0',
            'test-key',
            'dualstack'
        );

        $options = $request->getRequestOptions();
        $this->assertEquals('dualstack', $options['json']['ipAddressType']);
    }

    /**
     * 测试使用多个实例名称
     */
    public function testCreateInstanceRequest_withMultipleInstanceNames()
    {
        $instanceNames = ['instance1', 'instance2', 'instance3'];

        $request = new CreateInstanceRequest(
            $instanceNames,
            'us-east-1a',
            'amazon_linux_2',
            'nano_3_0',
            'test-key'
        );

        $options = $request->getRequestOptions();
        $this->assertEquals($instanceNames, $options['json']['instanceNames']);
    }

    /**
     * 测试设置凭证
     */
    public function testCreateInstanceRequest_withCredentials()
    {
        $request = new CreateInstanceRequest(
            ['test-instance'],
            'us-east-1a',
            'amazon_linux_2',
            'nano_3_0',
            'test-key'
        );

        $request->setCredentials('test-access', 'test-secret', 'us-west-2');

        $this->assertEquals('test-access', $request->getAccessKey());
        $this->assertEquals('test-secret', $request->getSecretKey());
        $this->assertEquals('us-west-2', $request->getRegion());
    }

    /**
     * 测试使用空实例名称数组
     */
    public function testCreateInstanceRequest_withEmptyInstanceNames()
    {
        $request = new CreateInstanceRequest(
            [],
            'us-east-1a',
            'amazon_linux_2',
            'nano_3_0',
            'test-key'
        );

        $options = $request->getRequestOptions();
        $this->assertEquals([], $options['json']['instanceNames']);
    }
}
