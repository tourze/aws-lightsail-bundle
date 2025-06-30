<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Integration\Controller\Admin\Action;

use AwsLightsailBundle\Controller\Admin\Action\SyncAlarmController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SyncAlarmControllerTest extends TestCase
{
    private SyncAlarmController $controller;

    protected function setUp(): void
    {
        $adminUrlGenerator = $this->createMock(AdminUrlGenerator::class);
        $this->controller = new SyncAlarmController($adminUrlGenerator);
    }

    public function testController_isInitializedCorrectly(): void
    {
        $this->assertInstanceOf(SyncAlarmController::class, $this->controller);
    }}
