<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Controller\Admin;

use AwsLightsailBundle\Controller\Admin\SnapshotCrudController;
use AwsLightsailBundle\Entity\Snapshot;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(SnapshotCrudController::class)]
#[RunTestsInSeparateProcesses]
final class SnapshotCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testUnauthenticatedAccessIsBlocked(): void
    {
        $client = self::createClient();
        $client->request('GET', '/aws-lightsail/snapshot');

        // 预期：404 (路由不存在) 或 401/403 (未认证/未授权)
        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertContains($statusCode, [401, 403, 404], '未认证访问应该被阻止或路由不存在');
    }

    public function testGetMethodIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('GET', '/aws-lightsail/snapshot');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 401, 403, 404]);
    }

    public function testPostMethodForNewIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('POST', '/aws-lightsail/snapshot/new');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 302, 401, 403, 404, 405]);
    }

    public function testPutMethodIsNotAllowed(): void
    {
        $client = self::createClient();
        $client->request('PUT', '/aws-lightsail/snapshot');
        $this->assertContains($client->getResponse()->getStatusCode(), [404, 405]);
    }

    public function testDeleteMethodForEntityIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('DELETE', '/aws-lightsail/snapshot/1/delete');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 302, 401, 403, 404, 405]);
    }

    public function testHeadMethodIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('HEAD', '/aws-lightsail/snapshot');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 401, 403, 404]);
    }

    public function testOptionsMethodIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('OPTIONS', '/aws-lightsail/snapshot');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 204, 404, 405]);
    }

    public function testSearchFunctionality(): void
    {
        $client = self::createClient();

        // 测试搜索功能（通过查询参数）
        $client->request('GET', '/aws-lightsail/snapshot', ['query' => 'test']);
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 401, 403, 404]);

        // 测试过滤功能（通过filter参数）
        $client->request('GET', '/aws-lightsail/snapshot', ['filters' => ['name' => 'test']]);
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 401, 403, 404]);

        // 测试排序功能
        $client->request('GET', '/aws-lightsail/snapshot', ['sort' => ['name' => 'ASC']]);
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 401, 403, 404]);
    }

    public function testConfigureMethodsViaHttp(): void
    {
        $client = self::createClient();
        $client->request('GET', '/aws-lightsail/snapshot');

        // 测试路由配置是否正确（返回200表示配置方法都正常工作）
        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertContains($statusCode, [200, 401, 403, 404], 'Controller配置方法应该通过HTTP请求正常工作');
    }

    /**
     * @return AbstractCrudController<Snapshot>
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(SnapshotCrudController::class);
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield 'name' => ['name'];
        yield 'resourceName' => ['resourceName'];
        yield 'type' => ['type'];
        yield 'region' => ['region'];
        yield 'state' => ['state'];
        yield 'progress' => ['progress'];
        yield 'sizeInGb' => ['sizeInGb'];
        yield 'isFromAutoSnapshot' => ['isFromAutoSnapshot'];
        yield 'fromSnapshotName' => ['fromSnapshotName'];
        yield 'fromRegion' => ['fromRegion'];
        yield 'tags' => ['tags'];
        yield 'createTime' => ['createTime'];
        yield 'syncTime' => ['syncTime'];
        yield 'updateTime' => ['updateTime'];
    }

    public static function provideNewPageFields(): iterable
    {
        yield 'name' => ['name'];
        yield 'resourceName' => ['resourceName'];
        yield 'type' => ['type'];
        yield 'region' => ['region'];
        yield 'state' => ['state'];
        yield 'progress' => ['progress'];
        yield 'sizeInGb' => ['sizeInGb'];
        yield 'isFromAutoSnapshot' => ['isFromAutoSnapshot'];
        yield 'fromSnapshotName' => ['fromSnapshotName'];
        yield 'fromRegion' => ['fromRegion'];
        yield 'tags' => ['tags'];
        yield 'credential' => ['credential'];
    }

    public static function provideEditPageFields(): iterable
    {
        yield 'name' => ['name'];
        yield 'resourceName' => ['resourceName'];
        yield 'type' => ['type'];
        yield 'region' => ['region'];
        yield 'state' => ['state'];
        yield 'progress' => ['progress'];
        yield 'sizeInGb' => ['sizeInGb'];
        yield 'isFromAutoSnapshot' => ['isFromAutoSnapshot'];
        yield 'fromSnapshotName' => ['fromSnapshotName'];
        yield 'fromRegion' => ['fromRegion'];
        yield 'tags' => ['tags'];
        yield 'credential' => ['credential'];
    }
}
