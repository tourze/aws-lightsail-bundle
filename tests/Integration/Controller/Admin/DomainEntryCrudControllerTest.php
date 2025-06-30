<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Integration\Controller\Admin;

use AwsLightsailBundle\Controller\Admin\DomainEntryCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\TestCase;

final class DomainEntryCrudControllerTest extends TestCase
{
    private DomainEntryCrudController $controller;

    protected function setUp(): void
    {
        $requestStack = $this->createMock(\Symfony\Component\HttpFoundation\RequestStack::class);
        $domainRepository = $this->createMock(\AwsLightsailBundle\Repository\DomainRepository::class);
        
        $this->controller = new DomainEntryCrudController($requestStack, $domainRepository);
    }

    public function testController_isInstanceOfAbstractCrudController(): void
    {
        $this->assertInstanceOf(AbstractCrudController::class, $this->controller);
    }

    public function testController_isInitializedCorrectly(): void
    {
        $this->assertInstanceOf(DomainEntryCrudController::class, $this->controller);
    }public function testGetEntityFqcn_returnsString(): void
    {
        $result = $this->controller::getEntityFqcn();
        
        $this->assertNotEmpty($result);
    }
}
