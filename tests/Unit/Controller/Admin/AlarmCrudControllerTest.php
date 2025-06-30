<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Controller\Admin;

use AwsLightsailBundle\Controller\Admin\AlarmCrudController;
use AwsLightsailBundle\Entity\Alarm;
use PHPUnit\Framework\TestCase;

final class AlarmCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn(): void
    {
        $this->assertSame(Alarm::class, AlarmCrudController::getEntityFqcn());
    }
}