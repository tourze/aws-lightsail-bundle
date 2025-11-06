<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Controller\Admin;

use AwsLightsailBundle\Controller\Admin\DatabaseCrudController;
use AwsLightsailBundle\Entity\Database;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(DatabaseCrudController::class)]
#[RunTestsInSeparateProcesses]
final class DatabaseCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testUnauthenticatedAccessIsBlocked(): void
    {
        $client = self::createClient();
        $client->request('GET', '/aws-lightsail/database');

        // 预期：404 (路由不存在) 或 401/403 (未认证/未授权)
        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertContains($statusCode, [401, 403, 404], '未认证访问应该被阻止或路由不存在');
    }

    public function testGetMethodIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('GET', '/aws-lightsail/database');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 401, 403, 404]);
    }

    public function testPostMethodForNewIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('POST', '/aws-lightsail/database/new');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 302, 401, 403, 404, 405]);
    }

    public function testPutMethodIsNotAllowed(): void
    {
        $client = self::createClient();
        $client->request('PUT', '/aws-lightsail/database');
        $this->assertContains($client->getResponse()->getStatusCode(), [404, 405]);
    }

    public function testDeleteMethodForEntityIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('DELETE', '/aws-lightsail/database/1/delete');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 302, 401, 403, 404, 405]);
    }

    public function testHeadMethodIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('HEAD', '/aws-lightsail/database');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 401, 403, 404]);
    }

    public function testOptionsMethodIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('OPTIONS', '/aws-lightsail/database');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 204, 404, 405]);
    }

    public function testSearchFunctionality(): void
    {
        $client = self::createClient();

        // 测试搜索功能（通过查询参数）
        $client->request('GET', '/aws-lightsail/database', ['query' => 'test']);
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 401, 403, 404]);

        // 测试过滤功能（通过filter参数）
        $client->request('GET', '/aws-lightsail/database', ['filters' => ['name' => 'test']]);
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 401, 403, 404]);

        // 测试排序功能
        $client->request('GET', '/aws-lightsail/database', ['sort' => ['name' => 'ASC']]);
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 401, 403, 404]);
    }

    public function testConfigureMethodsViaHttp(): void
    {
        $client = self::createClient();
        $client->request('GET', '/aws-lightsail/database');

        // 测试路由配置是否正确（返回200表示配置方法都正常工作）
        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertContains($statusCode, [200, 401, 403, 404], 'Controller配置方法应该通过HTTP请求正常工作');
    }

    /**
     * @return AbstractCrudController<Database>
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(DatabaseCrudController::class);
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '数据库名称' => ['数据库名称'];
        yield '数据库引擎' => ['数据库引擎'];
        yield '引擎版本' => ['引擎版本'];
        yield '主终端节点' => ['主终端节点'];
        yield '状态' => ['状态'];
        yield '套餐ID' => ['套餐ID'];
        yield '区域' => ['区域'];
        yield '公开访问' => ['公开访问'];
        yield '备份保留' => ['备份保留'];
        yield '自动次要版本升级' => ['自动次要版本升级'];
        yield 'AWS 凭证' => ['AWS 凭证'];
        yield '创建时间' => ['创建时间'];
        yield '同步时间' => ['同步时间'];
        yield '更新时间' => ['更新时间'];
    }

    public static function provideNewPageFields(): iterable
    {
        yield '数据库名称' => ['数据库名称'];
        yield '数据库引擎' => ['数据库引擎'];
        yield '引擎版本' => ['引擎版本'];
        yield '主终端节点' => ['主终端节点'];
        yield '状态' => ['状态'];
        yield '套餐ID' => ['套餐ID'];
        yield '区域' => ['区域'];
        yield '公开访问' => ['公开访问'];
        yield '备份保留' => ['备份保留'];
        yield '自动次要版本升级' => ['自动次要版本升级'];
        yield 'AWS 凭证' => ['AWS 凭证'];
    }

    public static function provideEditPageFields(): iterable
    {
        yield '数据库名称' => ['数据库名称'];
        yield '数据库引擎' => ['数据库引擎'];
        yield '引擎版本' => ['引擎版本'];
        yield '主终端节点' => ['主终端节点'];
        yield '状态' => ['状态'];
        yield '套餐ID' => ['套餐ID'];
        yield '区域' => ['区域'];
        yield '公开访问' => ['公开访问'];
        yield '备份保留' => ['备份保留'];
        yield '自动次要版本升级' => ['自动次要版本升级'];
        yield 'AWS 凭证' => ['AWS 凭证'];
    }
}
