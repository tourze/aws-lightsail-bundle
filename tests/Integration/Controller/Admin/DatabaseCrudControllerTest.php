<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Integration\Controller\Admin;

use AwsLightsailBundle\Controller\Admin\DatabaseCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\TestCase;

final class DatabaseCrudControllerTest extends TestCase
{
    private DatabaseCrudController $controller;

    protected function setUp(): void
    {
        $this->controller = new DatabaseCrudController();
    }

    public function testController_isInstanceOfAbstractCrudController(): void
    {
        $this->assertInstanceOf(AbstractCrudController::class, $this->controller);
    }

    public function testController_isInitializedCorrectly(): void
    {
        $this->assertInstanceOf(DatabaseCrudController::class, $this->controller);
    }public function testGetEntityFqcn_returnsString(): void
    {
        $result = $this->controller::getEntityFqcn();
        
        $this->assertNotEmpty($result);
    }
}
