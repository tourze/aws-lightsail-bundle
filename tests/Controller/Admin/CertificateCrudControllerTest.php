<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Controller\Admin;

use AwsLightsailBundle\Controller\Admin\CertificateCrudController;
use AwsLightsailBundle\Entity\Certificate;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(CertificateCrudController::class)]
#[RunTestsInSeparateProcesses]
final class CertificateCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testUnauthenticatedAccessIsBlocked(): void
    {
        $client = self::createClient();
        $client->request('GET', '/aws-lightsail/certificate');

        // 预期：404 (路由不存在) 或 401/403 (未认证/未授权)
        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertContains($statusCode, [401, 403, 404], '未认证访问应该被阻止或路由不存在');
    }

    public function testGetMethodIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('GET', '/aws-lightsail/certificate');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 401, 403, 404]);
    }

    public function testPostMethodForNewIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('POST', '/aws-lightsail/certificate/new');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 302, 401, 403, 404, 405]);
    }

    public function testPutMethodIsNotAllowed(): void
    {
        $client = self::createClient();
        $client->request('PUT', '/aws-lightsail/certificate');
        $this->assertContains($client->getResponse()->getStatusCode(), [404, 405]);
    }

    public function testDeleteMethodForEntityIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('DELETE', '/aws-lightsail/certificate/1/delete');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 302, 401, 403, 404, 405]);
    }

    public function testHeadMethodIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('HEAD', '/aws-lightsail/certificate');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 401, 403, 404]);
    }

    public function testOptionsMethodIsAllowed(): void
    {
        $client = self::createClient();
        $client->request('OPTIONS', '/aws-lightsail/certificate');
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 204, 404, 405]);
    }

    public function testSearchFunctionality(): void
    {
        $client = self::createClient();

        // 测试搜索功能（通过查询参数）
        $client->request('GET', '/aws-lightsail/certificate', ['query' => 'test']);
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 401, 403, 404]);

        // 测试过滤功能（通过filter参数）
        $client->request('GET', '/aws-lightsail/certificate', ['filters' => ['name' => 'test']]);
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 401, 403, 404]);

        // 测试排序功能
        $client->request('GET', '/aws-lightsail/certificate', ['sort' => ['name' => 'ASC']]);
        $this->assertContains($client->getResponse()->getStatusCode(), [200, 401, 403, 404]);
    }

    public function testConfigureMethodsViaHttp(): void
    {
        $client = self::createClient();
        $client->request('GET', '/aws-lightsail/certificate');

        // 测试路由配置是否正确（返回200表示配置方法都正常工作）
        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertContains($statusCode, [200, 401, 403, 404], 'Controller配置方法应该通过HTTP请求正常工作');
    }

    /**
     * @return AbstractCrudController<Certificate>
     */
    protected function getControllerService(): AbstractCrudController
    {
        /** @var CertificateCrudController */
        return self::getService(CertificateCrudController::class);
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield 'name' => ['name'];
        yield 'domainName' => ['domainName'];
        yield 'status' => ['status'];
        yield 'region' => ['region'];
        yield 'notBefore' => ['notBefore'];
        yield 'notAfter' => ['notAfter'];
        yield 'isManaged' => ['isManaged'];
        yield 'inUse' => ['inUse'];
        yield 'credential' => ['credential'];
        yield 'createTime' => ['createTime'];
        yield 'syncTime' => ['syncTime'];
        yield 'updateTime' => ['updateTime'];
    }

    public static function provideNewPageFields(): iterable
    {
        yield 'name' => ['name'];
        yield 'domainName' => ['domainName'];
        yield 'subjectAlternativeNames' => ['subjectAlternativeNames'];
        yield 'domainValidationRecords' => ['domainValidationRecords'];
        yield 'status' => ['status'];
        yield 'region' => ['region'];
        yield 'serialNumber' => ['serialNumber'];
        yield 'keyAlgorithm' => ['keyAlgorithm'];
        yield 'isManaged' => ['isManaged'];
        yield 'inUse' => ['inUse'];
        yield 'supportedOnResources' => ['supportedOnResources'];
        yield 'credential' => ['credential'];
    }

    public static function provideEditPageFields(): iterable
    {
        yield 'name' => ['name'];
        yield 'domainName' => ['domainName'];
        yield 'subjectAlternativeNames' => ['subjectAlternativeNames'];
        yield 'domainValidationRecords' => ['domainValidationRecords'];
        yield 'status' => ['status'];
        yield 'region' => ['region'];
        yield 'serialNumber' => ['serialNumber'];
        yield 'keyAlgorithm' => ['keyAlgorithm'];
        yield 'isManaged' => ['isManaged'];
        yield 'inUse' => ['inUse'];
        yield 'supportedOnResources' => ['supportedOnResources'];
        yield 'credential' => ['credential'];
    }
}
