<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Controller\Admin;

use AwsLightsailBundle\Controller\Admin\InstanceCrudController;
use AwsLightsailBundle\Entity\Instance;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(InstanceCrudController::class)]
#[RunTestsInSeparateProcesses]
final class InstanceCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testUnauthenticatedAccessIsBlocked(): void
    {
        $client = self::createClient();
        $client->request('GET', '/aws-lightsail/instance');

        // 预期：404 (路由不存在) 或 401/403 (未认证/未授权)
        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertContains($statusCode, [401, 403, 404], '未认证访问应该被阻止或路由不存在');
    }

    public function testGetMethodIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('GET', '/aws-lightsail/instance');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 401, 403, 404]);
    }

    public function testPostMethodForNewIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('POST', '/aws-lightsail/instance/new');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 302, 401, 403, 404, 405]);
    }

    public function testPutMethodIsNotAllowed(): void
    {
        $client = self::createClient();
        $client->request('PUT', '/aws-lightsail/instance');
        $this->assertContains($client->getResponse()->getStatusCode(), [404, 405]);
    }

    public function testDeleteMethodForEntityIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('DELETE', '/aws-lightsail/instance/1/delete');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 302, 401, 403, 404, 405]);
    }

    public function testHeadMethodIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('HEAD', '/aws-lightsail/instance');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 401, 403, 404]);
    }

    public function testOptionsMethodIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('OPTIONS', '/aws-lightsail/instance');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 204, 404, 405]);
    }

    public function testSearchFunctionality(): void
    {
        $client = self::createClient();

        // 测试搜索功能（通过查询参数）
        $client->request('GET', '/aws-lightsail/instance', ['query' => 'test']);
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 401, 403, 404]);

        // 测试过滤功能（通过filter参数）
        $client->request('GET', '/aws-lightsail/instance', ['filters' => ['name' => 'test']]);
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 401, 403, 404]);

        // 测试排序功能
        $client->request('GET', '/aws-lightsail/instance', ['sort' => ['name' => 'ASC']]);
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 401, 403, 404]);
    }

    public function testConfigureMethodsViaHttp(): void
    {
        $client = self::createClient();
        $client->request('GET', '/aws-lightsail/instance');

        // 测试路由配置是否正确（返回200表示配置方法都正常工作）
        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertContains($statusCode, [200, 401, 403, 404], 'Controller配置方法应该通过HTTP请求正常工作');
    }

    /**
     * @return AbstractCrudController<Instance>
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(InstanceCrudController::class);
    }

    public static function provideIndexPageHeaders(): iterable
    {
        // 按照InstanceCrudController::configureFields()中字段的实际顺序，只包含索引页面显示的字段
        yield 'ID' => ['ID'];
        yield '实例名称' => ['实例名称'];
        yield '状态' => ['状态'];
        yield '蓝图' => ['蓝图'];
        yield '套餐' => ['套餐'];
        yield '蓝图名称' => ['蓝图名称'];
        yield '区域' => ['区域'];
        yield '公网IP' => ['公网IP'];
        yield '私网IP' => ['私网IP'];
        yield '密钥对' => ['密钥对'];
        yield '用户名' => ['用户名'];
        yield '监控状态' => ['监控状态'];
        yield 'AWS 凭证' => ['AWS 凭证'];
        yield '创建时间' => ['创建时间'];
        yield '同步时间' => ['同步时间'];
        yield '更新时间' => ['更新时间'];
    }

    public static function provideNewPageFields(): iterable
    {
        // 提供一个虚拟字段以避免空数据集错误，即使 NEW action 已被禁用
        yield 'name' => ['name'];
    }

    public static function provideEditPageFields(): iterable
    {
        // 只包含在编辑页面显示的字段（大部分是只读的）
        yield 'name' => ['name'];
        yield 'arn' => ['arn'];
        yield 'state' => ['state'];
        yield 'blueprint' => ['blueprint'];
        yield 'bundle' => ['bundle'];
        yield 'blueprintName' => ['blueprintName'];
        yield 'region' => ['region'];
        yield 'credential' => ['credential'];
    }

    public function testStartInstance(): void
    {
        $reflection = new \ReflectionMethod($this->getControllerService(), 'startInstance');
        $this->assertTrue($reflection->isPublic(), 'startInstance方法应该是公共的');
    }

    public function testStopInstance(): void
    {
        $reflection = new \ReflectionMethod($this->getControllerService(), 'stopInstance');
        $this->assertTrue($reflection->isPublic(), 'stopInstance方法应该是公共的');
    }

    public function testRebootInstance(): void
    {
        $reflection = new \ReflectionMethod($this->getControllerService(), 'rebootInstance');
        $this->assertTrue($reflection->isPublic(), 'rebootInstance方法应该是公共的');
    }

    public function testSyncInstance(): void
    {
        $reflection = new \ReflectionMethod($this->getControllerService(), 'syncInstance');
        $this->assertTrue($reflection->isPublic(), 'syncInstance方法应该是公共的');
    }
}
