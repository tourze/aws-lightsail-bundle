<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Service;

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
use Symfony\Bundle\FrameworkBundle\Routing\AttributeRouteControllerLoader;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Routing\RouteCollection;
use Tourze\RoutingAutoLoaderBundle\Service\RoutingAutoLoaderInterface;

#[Autoconfigure(public: true)]
#[AutoconfigureTag(name: 'routing.loader')]
class AttributeControllerLoader extends Loader implements RoutingAutoLoaderInterface
{
    private AttributeRouteControllerLoader $controllerLoader;

    public function __construct()
    {
        parent::__construct();
        $this->controllerLoader = new AttributeRouteControllerLoader();
    }

    public function load(mixed $resource, ?string $type = null): RouteCollection
    {
        return $this->autoload();
    }

    public function supports(mixed $resource, ?string $type = null): bool
    {
        return false;
    }

    public function autoload(): RouteCollection
    {
        $collection = new RouteCollection();

        foreach ($this->getControllerClasses() as $controllerClass) {
            $collection->addCollection($this->controllerLoader->load($controllerClass));
        }

        return $collection;
    }

    /**
     * @return array<class-string>
     */
    private function getControllerClasses(): array
    {
        return [
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
    }
}
