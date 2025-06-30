<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Controller\Admin;

use AwsLightsailBundle\Controller\Admin\ContainerServiceCrudController;
use AwsLightsailBundle\Entity\ContainerService;
use PHPUnit\Framework\TestCase;

final class ContainerServiceCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn(): void
    {
        $this->assertSame(ContainerService::class, ContainerServiceCrudController::getEntityFqcn());
    }
}