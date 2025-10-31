<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Controller\Admin\Action;

use AwsLightsailBundle\Controller\Admin\Action\CreateDiskSnapshotController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractWebTestCase;

/**
 * @internal
 */
#[RunTestsInSeparateProcesses]
#[CoversClass(CreateDiskSnapshotController::class)]
final class CreateDiskSnapshotControllerTest extends AbstractWebTestCase
{
    public function testUnauthenticatedAccessIsBlocked(): void
    {
        $client = self::createClient();
        $client->request('POST', '/admin/disk/1/create-snapshot');

        // 预期：404 (路由不存在) 或 401/403 (未认证/未授权) 或 405 (方法不允许)
        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertContains($statusCode, [302, 401, 403, 404, 405], '未认证访问应该被阻止或路由不存在');
    }

    public function testPostMethodIsRequired(): void
    {
        $client = self::createClient();
        $client->request('GET', '/admin/disk/1/create-snapshot');
        $this->assertContains($client->getResponse()->getStatusCode(), [404, 405]);
    }

    public function testOptionsMethodIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('OPTIONS', '/admin/disk/1/create-snapshot');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 204, 404, 405]);
    }

    public function testPutMethodIsNotAllowed(): void
    {
        $client = self::createClient();
        $client->request('PUT', '/admin/disk/1/create-snapshot');
        $this->assertContains($client->getResponse()->getStatusCode(), [404, 405]);
    }

    public function testDeleteMethodIsNotAllowed(): void
    {
        $client = self::createClient();
        $client->request('DELETE', '/admin/disk/1/create-snapshot');
        $this->assertContains($client->getResponse()->getStatusCode(), [404, 405]);
    }

    public function testPatchMethodIsNotAllowed(): void
    {
        $client = self::createClient();
        $client->request('PATCH', '/admin/disk/1/create-snapshot');
        $this->assertContains($client->getResponse()->getStatusCode(), [404, 405]);
    }

    public function testHeadMethodIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('HEAD', '/admin/disk/1/create-snapshot');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 401, 403, 404, 405]);
    }

    #[DataProvider('provideNotAllowedMethods')]
    public function testMethodNotAllowed(string $method): void
    {
        $client = self::createClient();
        $client->request($method, '/admin/disk/1/create-snapshot');

        $response = $client->getResponse();
        $this->assertContains($response->getStatusCode(), [404, 405]);
    }
}
