<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Integration\Controller\Admin\Action;

use AwsLightsailBundle\Controller\Admin\Action\ToggleAlarmNotificationController;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ToggleAlarmNotificationControllerTest extends TestCase
{
    private ToggleAlarmNotificationController $controller;

    protected function setUp(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $adminUrlGenerator = $this->createMock(AdminUrlGenerator::class);
        $this->controller = new ToggleAlarmNotificationController($entityManager, $adminUrlGenerator);
    }

    public function testController_isInitializedCorrectly(): void
    {
        $this->assertInstanceOf(ToggleAlarmNotificationController::class, $this->controller);
    }
}
