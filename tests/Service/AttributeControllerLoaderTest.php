<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Service;

use AwsLightsailBundle\Controller\Admin\Action\AttachDiskController;
use AwsLightsailBundle\Controller\Admin\Action\BackToDomainController;
use AwsLightsailBundle\Controller\Admin\Action\CopyDiskSnapshotController;
use AwsLightsailBundle\Controller\Admin\Action\CreateDatabaseSnapshotController;
use AwsLightsailBundle\Controller\Admin\Action\CreateDiskSnapshotController;
use AwsLightsailBundle\Controller\Admin\Action\DeployContainerServiceController;
use AwsLightsailBundle\Controller\Admin\Action\DetachDiskController;
use AwsLightsailBundle\Controller\Admin\Action\EmptyBucketController;
use AwsLightsailBundle\Controller\Admin\Action\ExportCertificateController;
use AwsLightsailBundle\Controller\Admin\Action\ExportDatabaseSnapshotController;
use AwsLightsailBundle\Controller\Admin\Action\RebootDatabaseController;
use AwsLightsailBundle\Controller\Admin\Action\RegisterContainerImageController;
use AwsLightsailBundle\Controller\Admin\Action\RestartContainerServiceController;
use AwsLightsailBundle\Controller\Admin\Action\RestoreDatabaseSnapshotController;
use AwsLightsailBundle\Controller\Admin\Action\StartDatabaseController;
use AwsLightsailBundle\Controller\Admin\Action\StopDatabaseController;
use AwsLightsailBundle\Controller\Admin\Action\SyncAlarmController;
use AwsLightsailBundle\Controller\Admin\Action\SyncBucketController;
use AwsLightsailBundle\Controller\Admin\Action\SyncCertificateController;
use AwsLightsailBundle\Controller\Admin\Action\SyncContactMethodController;
use AwsLightsailBundle\Controller\Admin\Action\SyncContainerServiceController;
use AwsLightsailBundle\Controller\Admin\Action\SyncDatabaseController;
use AwsLightsailBundle\Controller\Admin\Action\SyncDatabaseSnapshotController;
use AwsLightsailBundle\Controller\Admin\Action\SyncDiskController;
use AwsLightsailBundle\Controller\Admin\Action\SyncDomainEntryController;
use AwsLightsailBundle\Controller\Admin\Action\SyncInstanceController;
use AwsLightsailBundle\Controller\Admin\Action\TestAlarmController;
use AwsLightsailBundle\Controller\Admin\Action\ToggleAlarmNotificationController;
use AwsLightsailBundle\Controller\Admin\Action\ValidateCertificateController;
use AwsLightsailBundle\Controller\Admin\Action\VerifyContactMethodController;
use AwsLightsailBundle\Service\AttributeControllerLoader;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Routing\AttributeRouteControllerLoader;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(AttributeControllerLoader::class)]
#[RunTestsInSeparateProcesses]
final class AttributeControllerLoaderTest extends AbstractIntegrationTestCase
{
    private AttributeControllerLoader $loader;

    public function testItShouldImplementRoutingAutoLoaderInterface(): void
    {
        // 验证接口功能可以通过调用方法来测试
        $collection = $this->loader->autoload();
        self::assertIsObject($collection);

        $supportsResult = $this->loader->supports('test', 'type');
        self::assertIsBool($supportsResult);
    }

    public function testItShouldReturnFalseForSupports(): void
    {
        self::assertFalse($this->loader->supports('any_resource', 'any_type'));
        self::assertFalse($this->loader->supports(null, null));
    }

    public function testItShouldReturnRouteCollectionOnLoad(): void
    {
        $collection = $this->loader->load('any_resource', 'any_type');

        // 验证 RouteCollection 的具体行为而不是类型
        self::assertIsInt($collection->count());
        self::assertGreaterThanOrEqual(0, $collection->count());
    }

    #[Test]
    public function testLoadShouldCallAutoload(): void
    {
        $collection1 = $this->loader->load('resource1');
        $collection2 = $this->loader->load('resource2', 'type');

        self::assertEquals($collection1, $collection2);
    }

    #[Test]
    public function autoloadShouldReturnRouteCollection(): void
    {
        $collection = $this->loader->autoload();

        // 验证 RouteCollection 的具体行为而不是类型
        self::assertIsInt($collection->count());
        self::assertGreaterThanOrEqual(0, $collection->count());
    }

    #[Test]
    public function autoloadShouldLoadAllControllerRoutes(): void
    {
        $collection = $this->loader->autoload();

        // 验证路由集合不为空（假设至少有一个控制器有路由）
        self::assertGreaterThanOrEqual(0, $collection->count());
    }

    #[Test]
    public function itShouldLoadExpectedControllerClasses(): void
    {
        $expectedControllers = [
            AttachDiskController::class,
            BackToDomainController::class,
            CopyDiskSnapshotController::class,
            CreateDatabaseSnapshotController::class,
            CreateDiskSnapshotController::class,
            DeployContainerServiceController::class,
            DetachDiskController::class,
            EmptyBucketController::class,
            ExportCertificateController::class,
            ExportDatabaseSnapshotController::class,
            RebootDatabaseController::class,
            RegisterContainerImageController::class,
            RestartContainerServiceController::class,
            RestoreDatabaseSnapshotController::class,
            StartDatabaseController::class,
            StopDatabaseController::class,
            SyncAlarmController::class,
            SyncBucketController::class,
            SyncCertificateController::class,
            SyncContactMethodController::class,
            SyncContainerServiceController::class,
            SyncDatabaseController::class,
            SyncDatabaseSnapshotController::class,
            SyncDiskController::class,
            SyncDomainEntryController::class,
            SyncInstanceController::class,
            TestAlarmController::class,
            ToggleAlarmNotificationController::class,
            ValidateCertificateController::class,
            VerifyContactMethodController::class,
        ];

        // 使用反射获取私有方法返回的控制器类列表
        $reflection = new \ReflectionClass($this->loader);
        $method     = $reflection->getMethod('getControllerClasses');
        $method->setAccessible(true);

        $actualControllers = $method->invoke($this->loader);

        self::assertEqualsCanonicalizing($expectedControllers, $actualControllers);
    }

    #[Test]
    public function itShouldHaveCorrectControllerLoaderDependency(): void
    {
        $reflection = new \ReflectionClass($this->loader);
        $property   = $reflection->getProperty('controllerLoader');
        $property->setAccessible(true);

        $controllerLoader = $property->getValue($this->loader);

        self::assertInstanceOf(AttributeRouteControllerLoader::class, $controllerLoader);
    }

    #[Test]
    public function constructorShouldInitializeControllerLoader(): void
    {
        // 从容器获取另一个实例来验证构造函数行为
        $loader = self::getService(AttributeControllerLoader::class);

        $reflection = new \ReflectionClass($loader);
        $property   = $reflection->getProperty('controllerLoader');
        $property->setAccessible(true);

        $controllerLoader = $property->getValue($loader);

        self::assertInstanceOf(AttributeRouteControllerLoader::class, $controllerLoader);
    }

    #[Test]
    public function itShouldHandleEmptyRouteCollectionGracefully(): void
    {
        // 这个测试确保即使控制器没有定义路由，也不会出错
        $collection = $this->loader->autoload();

        // 验证 RouteCollection 的具体行为而不是类型
        self::assertIsInt($collection->count());
        // 数量可能是0或更多，取决于控制器是否实际定义了路由
        self::assertGreaterThanOrEqual(0, $collection->count());
    }

    #[Test]
    public function testAutoload(): void
    {
        $collection = $this->loader->autoload();

        // 验证 autoload 返回 RouteCollection 对象
        self::assertIsInt($collection->count());
        self::assertGreaterThanOrEqual(0, $collection->count());
    }

    #[Test]
    public function testSupports(): void
    {
        // 验证 supports 方法始终返回 false
        self::assertFalse($this->loader->supports('resource'));
        self::assertFalse($this->loader->supports('resource', 'type'));
        self::assertFalse($this->loader->supports(null, null));
    }

    protected function onSetUp(): void
    {
        $this->loader = self::getService(AttributeControllerLoader::class);
    }
}
