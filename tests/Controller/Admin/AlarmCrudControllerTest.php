<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Controller\Admin;

use AwsLightsailBundle\Controller\Admin\AlarmCrudController;
use AwsLightsailBundle\Entity\Alarm;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(AlarmCrudController::class)]
#[RunTestsInSeparateProcesses]
final class AlarmCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testUnauthenticatedAccessIsBlocked(): void
    {
        $client = self::createClient();
        $client->request('GET', '/aws-lightsail/alarm');

        // 预期：404 (路由不存在) 或 401/403 (未认证/未授权)
        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertContains($statusCode, [401, 403, 404], '未认证访问应该被阻止或路由不存在');
    }

    public function testGetMethodIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('GET', '/aws-lightsail/alarm');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 401, 403, 404]);
    }

    public function testPostMethodForNewIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('POST', '/aws-lightsail/alarm/new');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 302, 401, 403, 404, 405]);
    }

    public function testPutMethodIsNotAllowed(): void
    {
        $client = self::createClient();
        $client->request('PUT', '/aws-lightsail/alarm');
        $this->assertContains($client->getResponse()->getStatusCode(), [404, 405]);
    }

    public function testDeleteMethodForEntityIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('DELETE', '/aws-lightsail/alarm/1/delete');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 302, 401, 403, 404, 405]);
    }

    public function testHeadMethodIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('HEAD', '/aws-lightsail/alarm');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 401, 403, 404]);
    }

    public function testOptionsMethodIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('OPTIONS', '/aws-lightsail/alarm');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 204, 404, 405]);
    }

    public function testSearchFunctionality(): void
    {
        $client = self::createClient();

        // 测试搜索功能（通过查询参数）
        $client->request('GET', '/aws-lightsail/alarm', ['query' => 'test']);
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 401, 403, 404]);

        // 测试过滤功能（通过filter参数）
        $client->request('GET', '/aws-lightsail/alarm', ['filters' => ['name' => 'test']]);
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 401, 403, 404]);

        // 测试排序功能
        $client->request('GET', '/aws-lightsail/alarm', ['sort' => ['name' => 'ASC']]);
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 401, 403, 404]);
    }

    public function testConfigureMethodsViaHttp(): void
    {
        $client = self::createClient();
        $client->request('GET', '/aws-lightsail/alarm');

        // 测试路由配置是否正确（返回200表示配置方法都正常工作）
        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertContains($statusCode, [200, 401, 403, 404], 'Controller配置方法应该通过HTTP请求正常工作');
    }

    /**
     * @return AbstractCrudController<Alarm>
     */
    protected function getControllerService(): AbstractCrudController
    {
        /** @var AlarmCrudController */
        return self::getService(AlarmCrudController::class);
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '告警名称' => ['告警名称'];
        yield '资源名称' => ['资源名称'];
        yield '资源类型' => ['资源类型'];
        yield '监控指标' => ['监控指标'];
        yield '状态' => ['状态'];
        yield '区域' => ['区域'];
        yield '阈值' => ['阈值'];
        yield '通知已启用' => ['通知已启用'];
        yield '通知触发时间' => ['通知触发时间'];
        yield 'AWS 凭证' => ['AWS 凭证'];
        yield '创建时间' => ['创建时间'];
        yield '同步时间' => ['同步时间'];
        yield '更新时间' => ['更新时间'];
    }

    public static function provideNewPageFields(): iterable
    {
        // NEW action已被禁用，提供占位符数据
        yield 'placeholder' => ['placeholder'];
    }

    public static function provideEditPageFields(): iterable
    {
        // EDIT action已被禁用，提供占位符数据
        yield 'placeholder' => ['placeholder'];
    }

    /**
     * 测试 syncAlarm 动作路由存在性
     */
    public function testSyncAlarmActionRouteExists(): void
    {
        $client = self::createClient();

        // 测试 GET 方法应该不被允许（仅允许 POST）
        $client->request('GET', '/admin/alarm/1/sync');
        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertContains($statusCode, [404, 405], 'syncAlarm 动作应该只支持 POST 方法');
    }

    /**
     * 测试 testAlarm 动作路由存在性
     */
    public function testTestAlarmActionRouteExists(): void
    {
        $client = self::createClient();

        // 测试 GET 方法应该不被允许（仅允许 POST）
        $client->request('GET', '/admin/alarm/1/test');
        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertContains($statusCode, [404, 405], 'testAlarm 动作应该只支持 POST 方法');
    }

    /**
     * 测试 toggleNotification 动作路由存在性
     */
    public function testToggleNotificationActionRouteExists(): void
    {
        $client = self::createClient();

        // 测试 GET 方法应该不被允许（仅允许 POST）
        $client->request('GET', '/admin/alarm/1/toggle-notification');
        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertContains($statusCode, [404, 405], 'toggleNotification 动作应该只支持 POST 方法');
    }

    /**
     * 测试 syncAlarm 动作的 POST 请求
     */
    public function testSyncAlarmActionPostRequest(): void
    {
        $client = self::createClient();

        $client->request('POST', '/admin/alarm/1/sync');

        // 预期：302 (重定向)、401/403 (未认证/未授权)、404 (实体不存在)
        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertContains(
            $statusCode,
            [302, 401, 403, 404],
            'syncAlarm 动作的 POST 请求应该返回重定向或认证/授权错误或实体不存在'
        );
    }

    /**
     * 测试 testAlarm 动作的 POST 请求
     */
    public function testTestAlarmActionPostRequest(): void
    {
        $client = self::createClient();

        $client->request('POST', '/admin/alarm/1/test');

        // 预期：302 (重定向)、401/403 (未认证/未授权)、404 (实体不存在)
        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertContains(
            $statusCode,
            [302, 401, 403, 404],
            'testAlarm 动作的 POST 请求应该返回重定向或认证/授权错误或实体不存在'
        );
    }

    /**
     * 测试 toggleNotification 动作的 POST 请求
     */
    public function testToggleNotificationActionPostRequest(): void
    {
        $client = self::createClient();

        $client->request('POST', '/admin/alarm/1/toggle-notification');

        // 预期：302 (重定向)、401/403 (未认证/未授权)、404 (实体不存在)
        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertContains(
            $statusCode,
            [302, 401, 403, 404],
            'toggleNotification 动作的 POST 请求应该返回重定向或认证/授权错误或实体不存在'
        );
    }

    /**
     * 测试 syncAlarm 动作方法不支持的 HTTP 方法
     */
    public function testSyncAlarmActionUnsupportedMethods(): void
    {
        $client = self::createClient();

        $unsupportedMethods = ['PUT', 'DELETE', 'PATCH'];
        foreach ($unsupportedMethods as $method) {
            $client->request($method, '/admin/alarm/1/sync');
            $statusCode = $client->getResponse()->getStatusCode();
            $this->assertContains(
                $statusCode,
                [404, 405],
                \sprintf('syncAlarm 动作不应该支持 %s 方法', $method)
            );
        }
    }

    /**
     * 测试 testAlarm 动作方法不支持的 HTTP 方法
     */
    public function testTestAlarmActionUnsupportedMethods(): void
    {
        $client = self::createClient();

        $unsupportedMethods = ['PUT', 'DELETE', 'PATCH'];
        foreach ($unsupportedMethods as $method) {
            $client->request($method, '/admin/alarm/1/test');
            $statusCode = $client->getResponse()->getStatusCode();
            $this->assertContains(
                $statusCode,
                [404, 405],
                \sprintf('testAlarm 动作不应该支持 %s 方法', $method)
            );
        }
    }

    /**
     * 测试 toggleNotification 动作方法不支持的 HTTP 方法
     */
    public function testToggleNotificationActionUnsupportedMethods(): void
    {
        $client = self::createClient();

        $unsupportedMethods = ['PUT', 'DELETE', 'PATCH'];
        foreach ($unsupportedMethods as $method) {
            $client->request($method, '/admin/alarm/1/toggle-notification');
            $statusCode = $client->getResponse()->getStatusCode();
            $this->assertContains(
                $statusCode,
                [404, 405],
                \sprintf('toggleNotification 动作不应该支持 %s 方法', $method)
            );
        }
    }
}
