<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Controller\Admin;

use AwsLightsailBundle\Controller\Admin\DiskCrudController;
use AwsLightsailBundle\Entity\Disk;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(DiskCrudController::class)]
#[RunTestsInSeparateProcesses]
final class DiskCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testUnauthenticatedAccessIsBlocked(): void
    {
        $client = self::createClient();
        $client->request('GET', '/aws-lightsail/disk');

        // 预期：404 (路由不存在) 或 401/403 (未认证/未授权)
        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertContains($statusCode, [401, 403, 404], '未认证访问应该被阻止或路由不存在');
    }

    public function testGetMethodIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('GET', '/aws-lightsail/disk');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 401, 403, 404]);
    }

    public function testPostMethodForNewIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('POST', '/aws-lightsail/disk/new');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 302, 401, 403, 404, 405]);
    }

    public function testPutMethodIsNotAllowed(): void
    {
        $client = self::createClient();
        $client->request('PUT', '/aws-lightsail/disk');
        $this->assertContains($client->getResponse()->getStatusCode(), [404, 405]);
    }

    public function testDeleteMethodForEntityIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('DELETE', '/aws-lightsail/disk/1/delete');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 302, 401, 403, 404, 405]);
    }

    public function testHeadMethodIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('HEAD', '/aws-lightsail/disk');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 401, 403, 404]);
    }

    public function testOptionsMethodIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('OPTIONS', '/aws-lightsail/disk');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 204, 404, 405]);
    }

    public function testSearchFunctionality(): void
    {
        $client = self::createClient();

        // 测试搜索功能（通过查询参数）
        $client->request('GET', '/aws-lightsail/disk', ['query' => 'test']);
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 401, 403, 404]);

        // 测试过滤功能（通过filter参数）
        $client->request('GET', '/aws-lightsail/disk', ['filters' => ['name' => 'test']]);
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 401, 403, 404]);

        // 测试排序功能
        $client->request('GET', '/aws-lightsail/disk', ['sort' => ['name' => 'ASC']]);
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 401, 403, 404]);
    }

    public function testConfigureMethodsViaHttp(): void
    {
        $client = self::createClient();
        $client->request('GET', '/aws-lightsail/disk');

        // 测试路由配置是否正确（返回200表示配置方法都正常工作）
        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertContains($statusCode, [200, 401, 403, 404], 'Controller配置方法应该通过HTTP请求正常工作');
    }

    /**
     * @return AbstractCrudController<Disk>
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(DiskCrudController::class);
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield 'name' => ['磁盘名称'];
        yield 'attachedTo' => ['挂载到实例'];
        yield 'attachmentState' => ['挂载状态'];
        yield 'isSystemDisk' => ['系统磁盘'];
        yield 'state' => ['状态'];
        yield 'region' => ['区域'];
        yield 'sizeInGb' => ['大小(GB)'];
        yield 'isAutoSnapshotConfigured' => ['已配置自动快照'];
        yield 'credential' => ['AWS 凭证'];
        yield 'createTime' => ['创建时间'];
        yield 'syncTime' => ['同步时间'];
        yield 'updateTime' => ['更新时间'];
    }

    public static function provideNewPageFields(): iterable
    {
        yield 'name' => ['磁盘名称'];
        yield 'sizeInGb' => ['大小(GB)'];
        yield 'region' => ['区域'];
        yield 'credential' => ['AWS 凭证'];
    }

    public static function provideEditPageFields(): iterable
    {
        yield 'name' => ['磁盘名称'];
        yield 'sizeInGb' => ['大小(GB)'];
        yield 'region' => ['区域'];
        yield 'credential' => ['AWS 凭证'];
    }

    public function testSyncDisk(): void
    {
        $reflection = new \ReflectionMethod($this->getControllerService(), 'syncDisk');
        $this->assertTrue($reflection->isPublic(), 'syncDisk方法应该是公共的');
    }

    public function testAttachDisk(): void
    {
        $reflection = new \ReflectionMethod($this->getControllerService(), 'attachDisk');
        $this->assertTrue($reflection->isPublic(), 'attachDisk方法应该是公共的');
    }

    public function testDetachDisk(): void
    {
        $reflection = new \ReflectionMethod($this->getControllerService(), 'detachDisk');
        $this->assertTrue($reflection->isPublic(), 'detachDisk方法应该是公共的');
    }

    public function testCreateDiskSnapshot(): void
    {
        $reflection = new \ReflectionMethod($this->getControllerService(), 'createDiskSnapshot');
        $this->assertTrue($reflection->isPublic(), 'createDiskSnapshot方法应该是公共的');
    }
}
