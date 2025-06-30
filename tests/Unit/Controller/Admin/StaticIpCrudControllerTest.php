<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Controller\Admin;

use AwsLightsailBundle\Controller\Admin\StaticIpCrudController;
use AwsLightsailBundle\Entity\StaticIp;
use PHPUnit\Framework\TestCase;

final class StaticIpCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn(): void
    {
        $this->assertSame(StaticIp::class, StaticIpCrudController::getEntityFqcn());
    }
}