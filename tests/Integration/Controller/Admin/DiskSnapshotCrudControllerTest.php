<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Integration\Controller\Admin;

use AwsLightsailBundle\Controller\Admin\DiskSnapshotCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\TestCase;

final class DiskSnapshotCrudControllerTest extends TestCase
{
    private DiskSnapshotCrudController $controller;

    protected function setUp(): void
    {
        $this->controller = new DiskSnapshotCrudController();
    }

    public function testController_isInstanceOfAbstractCrudController(): void
    {
        $this->assertInstanceOf(AbstractCrudController::class, $this->controller);
    }

    public function testController_isInitializedCorrectly(): void
    {
        $this->assertInstanceOf(DiskSnapshotCrudController::class, $this->controller);
    }public function testGetEntityFqcn_returnsString(): void
    {
        $result = $this->controller::getEntityFqcn();
        
        $this->assertNotEmpty($result);
    }
}
