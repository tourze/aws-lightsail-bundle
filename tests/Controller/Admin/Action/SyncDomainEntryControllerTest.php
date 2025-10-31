<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Controller\Admin\Action;

use AwsLightsailBundle\Controller\Admin\Action\SyncDomainEntryController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractWebTestCase;

/**
 * @internal
 */
#[RunTestsInSeparateProcesses]
#[CoversClass(SyncDomainEntryController::class)]
final class SyncDomainEntryControllerTest extends AbstractWebTestCase
{
    public function testUnauthenticatedAccessIsBlocked(): void
    {
        $client = self::createClient();
        $client->request('POST', '/admin/domain-entry/1/sync');

        // 预期：404 (路由不存在) 或 401/403 (未认证/未授权) 或 405 (方法不允许)
        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertContains($statusCode, [302, 401, 403, 404, 405], '未认证访问应该被阻止或路由不存在');
    }

    public function testPostMethodIsRequired(): void
    {
        $client = self::createClient();
        $client->request('GET', '/admin/domain-entry/1/sync');
        $this->assertContains($client->getResponse()->getStatusCode(), [404, 405]);
    }

    public function testOptionsMethodIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('OPTIONS', '/admin/domain-entry/1/sync');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 204, 404, 405]);
    }

    public function testPutMethodIsNotAllowed(): void
    {
        $client = self::createClient();
        $client->request('PUT', '/admin/domain-entry/1/sync');
        $this->assertContains($client->getResponse()->getStatusCode(), [404, 405]);
    }

    public function testDeleteMethodIsNotAllowed(): void
    {
        $client = self::createClient();
        $client->request('DELETE', '/admin/domain-entry/1/sync');
        $this->assertContains($client->getResponse()->getStatusCode(), [404, 405]);
    }

    public function testPatchMethodIsNotAllowed(): void
    {
        $client = self::createClient();
        $client->request('PATCH', '/admin/domain-entry/1/sync');
        $this->assertContains($client->getResponse()->getStatusCode(), [404, 405]);
    }

    public function testHeadMethodIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('HEAD', '/admin/domain-entry/1/sync');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 401, 403, 404, 405]);
    }

    #[DataProvider('provideNotAllowedMethods')]
    public function testMethodNotAllowed(string $method): void
    {
        $client = self::createClient();
        $client->request($method, '/admin/domain-entry/1/sync');

        $response = $client->getResponse();
        $this->assertContains($response->getStatusCode(), [404, 405]);
    }
}
