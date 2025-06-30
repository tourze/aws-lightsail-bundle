<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Integration\Controller\Admin\Action;

use AwsLightsailBundle\Controller\Admin\Action\CopyDiskSnapshotController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CopyDiskSnapshotControllerTest extends TestCase
{
    private CopyDiskSnapshotController $controller;
    private AdminUrlGenerator $adminUrlGenerator;

    protected function setUp(): void
    {
        $this->adminUrlGenerator = $this->createMock(AdminUrlGenerator::class);
        $this->controller = new CopyDiskSnapshotController($this->adminUrlGenerator);
    }

    public function testController_isInitializedCorrectly(): void
    {
        $this->assertInstanceOf(CopyDiskSnapshotController::class, $this->controller);
    }

}
