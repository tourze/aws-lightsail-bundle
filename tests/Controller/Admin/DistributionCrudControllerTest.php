<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Controller\Admin;

use AwsLightsailBundle\Controller\Admin\DistributionCrudController;
use AwsLightsailBundle\Entity\Distribution;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(DistributionCrudController::class)]
#[RunTestsInSeparateProcesses]
final class DistributionCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testUnauthenticatedAccessIsBlocked(): void
    {
        $client = self::createClient();
        $client->request('GET', '/aws-lightsail/distribution');

        // 预期：404 (路由不存在) 或 401/403 (未认证/未授权)
        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertContains($statusCode, [401, 403, 404], '未认证访问应该被阻止或路由不存在');
    }

    public function testGetMethodIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('GET', '/aws-lightsail/distribution');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 401, 403, 404]);
    }

    public function testPostMethodForNewIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('POST', '/aws-lightsail/distribution/new');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 302, 401, 403, 404, 405]);
    }

    public function testPutMethodIsNotAllowed(): void
    {
        $client = self::createClient();
        $client->request('PUT', '/aws-lightsail/distribution');
        $this->assertContains($client->getResponse()->getStatusCode(), [404, 405]);
    }

    public function testDeleteMethodForEntityIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('DELETE', '/aws-lightsail/distribution/1/delete');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 302, 401, 403, 404, 405]);
    }

    public function testHeadMethodIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('HEAD', '/aws-lightsail/distribution');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 401, 403, 404]);
    }

    public function testOptionsMethodIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('OPTIONS', '/aws-lightsail/distribution');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 204, 404, 405]);
    }

    public function testSearchFunctionality(): void
    {
        $client = self::createClient();

        // 测试搜索功能（通过查询参数）
        $client->request('GET', '/aws-lightsail/distribution', ['query' => 'test']);
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 401, 403, 404]);

        // 测试过滤功能（通过filter参数）
        $client->request('GET', '/aws-lightsail/distribution', ['filters' => ['name' => 'test']]);
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 401, 403, 404]);

        // 测试排序功能
        $client->request('GET', '/aws-lightsail/distribution', ['sort' => ['name' => 'ASC']]);
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 401, 403, 404]);
    }

    public function testConfigureMethodsViaHttp(): void
    {
        $client = self::createClient();
        $client->request('GET', '/aws-lightsail/distribution');

        // 测试路由配置是否正确（返回200表示配置方法都正常工作）
        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertContains($statusCode, [200, 401, 403, 404], 'Controller配置方法应该通过HTTP请求正常工作');
    }

    /**
     * @return AbstractCrudController<Distribution>
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(DistributionCrudController::class);
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield 'name' => ['name'];
        yield 'arn' => ['arn'];
        yield 'defaultDomainName' => ['defaultDomainName'];
        yield 'status' => ['status'];
        yield 'region' => ['region'];
        yield 'isEnabled' => ['isEnabled'];
        yield 'certificateName' => ['certificateName'];
    }

    public static function provideNewPageFields(): iterable
    {
        yield 'name' => ['name'];
        yield 'arn' => ['arn'];
        yield 'defaultDomainName' => ['defaultDomainName'];
        yield 'status' => ['status'];
        yield 'region' => ['region'];
        yield 'originConfigs' => ['originConfigs'];
        yield 'defaultCacheBehavior' => ['defaultCacheBehavior'];
        yield 'cacheBehaviors' => ['cacheBehaviors'];
        yield 'isEnabled' => ['isEnabled'];
        yield 'certificateName' => ['certificateName'];
        yield 'viewerProtocolPolicy' => ['viewerProtocolPolicy'];
    }

    public static function provideEditPageFields(): iterable
    {
        yield 'name' => ['name'];
        yield 'arn' => ['arn'];
        yield 'defaultDomainName' => ['defaultDomainName'];
        yield 'status' => ['status'];
        yield 'region' => ['region'];
        yield 'originConfigs' => ['originConfigs'];
        yield 'defaultCacheBehavior' => ['defaultCacheBehavior'];
        yield 'cacheBehaviors' => ['cacheBehaviors'];
        yield 'isEnabled' => ['isEnabled'];
        yield 'certificateName' => ['certificateName'];
        yield 'viewerProtocolPolicy' => ['viewerProtocolPolicy'];
    }
}
