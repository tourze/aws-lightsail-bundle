<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Integration\Controller\Admin\Action;

use AwsLightsailBundle\Controller\Admin\Action\SyncInstanceController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SyncInstanceControllerTest extends TestCase
{
    private SyncInstanceController $controller;

    protected function setUp(): void
    {
        $adminUrlGenerator = $this->createMock(AdminUrlGenerator::class);
        $this->controller = new SyncInstanceController($adminUrlGenerator);
    }

    public function testController_isInitializedCorrectly(): void
    {
        $this->assertInstanceOf(SyncInstanceController::class, $this->controller);
    }

}
