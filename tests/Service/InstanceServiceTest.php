<?php

namespace AwsLightsailBundle\Tests\Service;

use AwsLightsailBundle\Client\LightsailApiClient;
use AwsLightsailBundle\Request\CreateInstanceRequest;
use AwsLightsailBundle\Request\DeleteInstanceRequest;
use AwsLightsailBundle\Request\GetInstanceRequest;
use AwsLightsailBundle\Request\GetInstanceStateRequest;
use AwsLightsailBundle\Request\StartInstanceRequest;
use AwsLightsailBundle\Request\StopInstanceRequest;
use AwsLightsailBundle\Service\InstanceService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class InstanceServiceTest extends TestCase
{
    private LightsailApiClient $apiClient;
    private LoggerInterface $logger;
    private InstanceService $service;

    protected function setUp(): void
    {
        $this->apiClient = $this->createMock(LightsailApiClient::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->service = new InstanceService($this->apiClient, $this->logger);
    }

    /**
     * 测试创建实例
     */
    public function testCreateInstance()
    {
        $expectedResponse = [
            'operations' => [
                [
                    'id' => 'operation-id',
                    'status' => 'Succeeded',
                    'resourceType' => 'Instance',
                    'resourceName' => 'test-instance'
                ]
            ]
        ];

        $this->apiClient->expects($this->once())
            ->method('request')
            ->with($this->callback(function ($request) {
                return $request instanceof CreateInstanceRequest
                    && $request->getAccessKey() === 'test-access'
                    && $request->getSecretKey() === 'test-secret'
                    && $request->getRegion() === 'us-east-1';
            }))
            ->willReturn($expectedResponse);

        $this->logger->expects($this->exactly(2))
            ->method('info');

        $result = $this->service->createInstance(
            'test-instance',
            'us-east-1a',
            'amazon_linux_2',
            'nano_3_0',
            'test-key',
            'test-access',
            'test-secret',
            'us-east-1'
        );

        $this->assertEquals($expectedResponse, $result);
    }

    /**
     * 测试创建实例 - 异常情况
     */
    public function testCreateInstance_withException()
    {
        $expectedException = new \Exception('测试异常');

        $this->apiClient->expects($this->once())
            ->method('request')
            ->willThrowException($expectedException);

        $this->logger->expects($this->once())
            ->method('info');

        $this->logger->expects($this->once())
            ->method('error')
            ->with(
                $this->equalTo('创建实例失败'),
                $this->callback(function ($context) use ($expectedException) {
                    return isset($context['exception'])
                        && $context['exception'] === $expectedException
                        && isset($context['instanceName'])
                        && $context['instanceName'] === 'test-instance';
                })
            );

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('测试异常');

        $this->service->createInstance(
            'test-instance',
            'us-east-1a',
            'amazon_linux_2',
            'nano_3_0',
            'test-key',
            'test-access',
            'test-secret',
            'us-east-1'
        );
    }

    /**
     * 测试获取实例信息
     */
    public function testGetInstance()
    {
        $expectedResponse = [
            'instance' => [
                'name' => 'test-instance',
                'state' => [
                    'name' => 'running'
                ]
            ]
        ];

        $this->apiClient->expects($this->once())
            ->method('request')
            ->with($this->callback(function ($request) {
                return $request instanceof GetInstanceRequest
                    && $request->getAccessKey() === 'test-access'
                    && $request->getSecretKey() === 'test-secret'
                    && $request->getRegion() === 'us-east-1';
            }))
            ->willReturn($expectedResponse);

        $result = $this->service->getInstance(
            'test-instance',
            'test-access',
            'test-secret',
            'us-east-1'
        );

        $this->assertEquals($expectedResponse, $result);
    }

    /**
     * 测试获取实例信息 - 异常情况
     */
    public function testGetInstance_withException()
    {
        $expectedException = new \Exception('测试异常');

        $this->apiClient->expects($this->once())
            ->method('request')
            ->willThrowException($expectedException);

        $this->logger->expects($this->once())
            ->method('error')
            ->with(
                $this->equalTo('获取实例信息失败'),
                $this->callback(function ($context) use ($expectedException) {
                    return isset($context['exception'])
                        && $context['exception'] === $expectedException
                        && isset($context['instanceName'])
                        && $context['instanceName'] === 'test-instance';
                })
            );

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('测试异常');

        $this->service->getInstance(
            'test-instance',
            'test-access',
            'test-secret',
            'us-east-1'
        );
    }

    /**
     * 测试获取实例状态
     */
    public function testGetInstanceState()
    {
        $expectedResponse = [
            'state' => [
                'name' => 'running',
                'code' => 16
            ]
        ];

        $this->apiClient->expects($this->once())
            ->method('request')
            ->with($this->callback(function ($request) {
                return $request instanceof GetInstanceStateRequest
                    && $request->getAccessKey() === 'test-access'
                    && $request->getSecretKey() === 'test-secret'
                    && $request->getRegion() === 'us-east-1';
            }))
            ->willReturn($expectedResponse);

        $result = $this->service->getInstanceState(
            'test-instance',
            'test-access',
            'test-secret',
            'us-east-1'
        );

        $this->assertEquals($expectedResponse, $result);
    }

    /**
     * 测试启动实例
     */
    public function testStartInstance()
    {
        $expectedResponse = [
            'operations' => [
                [
                    'id' => 'operation-id',
                    'status' => 'Succeeded',
                    'resourceType' => 'Instance',
                    'resourceName' => 'test-instance'
                ]
            ]
        ];

        $this->apiClient->expects($this->once())
            ->method('request')
            ->with($this->callback(function ($request) {
                return $request instanceof StartInstanceRequest
                    && $request->getAccessKey() === 'test-access'
                    && $request->getSecretKey() === 'test-secret'
                    && $request->getRegion() === 'us-east-1';
            }))
            ->willReturn($expectedResponse);

        $result = $this->service->startInstance(
            'test-instance',
            'test-access',
            'test-secret',
            'us-east-1'
        );

        $this->assertEquals($expectedResponse, $result);
    }

    /**
     * 测试停止实例
     */
    public function testStopInstance()
    {
        $expectedResponse = [
            'operations' => [
                [
                    'id' => 'operation-id',
                    'status' => 'Succeeded',
                    'resourceType' => 'Instance',
                    'resourceName' => 'test-instance'
                ]
            ]
        ];

        $this->apiClient->expects($this->once())
            ->method('request')
            ->with($this->callback(function ($request) {
                return $request instanceof StopInstanceRequest
                    && $request->getAccessKey() === 'test-access'
                    && $request->getSecretKey() === 'test-secret'
                    && $request->getRegion() === 'us-east-1';
            }))
            ->willReturn($expectedResponse);

        $result = $this->service->stopInstance(
            'test-instance',
            true,
            'test-access',
            'test-secret',
            'us-east-1'
        );

        $this->assertEquals($expectedResponse, $result);
    }

    /**
     * 测试删除实例
     */
    public function testDeleteInstance()
    {
        $expectedResponse = [
            'operations' => [
                [
                    'id' => 'operation-id',
                    'status' => 'Succeeded',
                    'resourceType' => 'Instance',
                    'resourceName' => 'test-instance'
                ]
            ]
        ];

        $this->apiClient->expects($this->once())
            ->method('request')
            ->with($this->callback(function ($request) {
                return $request instanceof DeleteInstanceRequest
                    && $request->getAccessKey() === 'test-access'
                    && $request->getSecretKey() === 'test-secret'
                    && $request->getRegion() === 'us-east-1';
            }))
            ->willReturn($expectedResponse);

        $result = $this->service->deleteInstance(
            'test-instance',
            true,
            'test-access',
            'test-secret',
            'us-east-1'
        );

        $this->assertEquals($expectedResponse, $result);
    }
}
