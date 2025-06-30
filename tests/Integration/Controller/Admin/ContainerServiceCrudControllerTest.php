<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Integration\Controller\Admin;

use AwsLightsailBundle\Controller\Admin\ContainerServiceCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\TestCase;

final class ContainerServiceCrudControllerTest extends TestCase
{
    private ContainerServiceCrudController $controller;

    protected function setUp(): void
    {
        $this->controller = new ContainerServiceCrudController();
    }

    public function testController_isInstanceOfAbstractCrudController(): void
    {
        $this->assertInstanceOf(AbstractCrudController::class, $this->controller);
    }

    public function testController_isInitializedCorrectly(): void
    {
        $this->assertInstanceOf(ContainerServiceCrudController::class, $this->controller);
    }public function testGetEntityFqcn_returnsString(): void
    {
        $result = $this->controller::getEntityFqcn();
        
        $this->assertNotEmpty($result);
    }
}
