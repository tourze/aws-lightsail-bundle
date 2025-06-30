<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Integration\Controller\Admin;

use AwsLightsailBundle\Controller\Admin\KeyPairCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\TestCase;

final class KeyPairCrudControllerTest extends TestCase
{
    private KeyPairCrudController $controller;

    protected function setUp(): void
    {
        $this->controller = new KeyPairCrudController();
    }

    public function testController_isInstanceOfAbstractCrudController(): void
    {
        $this->assertInstanceOf(AbstractCrudController::class, $this->controller);
    }

    public function testController_isInitializedCorrectly(): void
    {
        $this->assertInstanceOf(KeyPairCrudController::class, $this->controller);
    }public function testGetEntityFqcn_returnsString(): void
    {
        $result = $this->controller::getEntityFqcn();
        
        $this->assertNotEmpty($result);
    }
}
