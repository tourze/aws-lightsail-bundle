<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Integration\Controller\Admin\Action;

use AwsLightsailBundle\Controller\Admin\Action\StopDatabaseController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class StopDatabaseControllerTest extends TestCase
{
    private StopDatabaseController $controller;

    protected function setUp(): void
    {
        $adminUrlGenerator = $this->createMock(AdminUrlGenerator::class);
        $this->controller = new StopDatabaseController($adminUrlGenerator);
    }

    public function testController_isInitializedCorrectly(): void
    {
        $this->assertInstanceOf(StopDatabaseController::class, $this->controller);
    }}
