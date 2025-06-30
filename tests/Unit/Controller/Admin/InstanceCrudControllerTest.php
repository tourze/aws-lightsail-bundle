<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Controller\Admin;

use AwsLightsailBundle\Controller\Admin\InstanceCrudController;
use AwsLightsailBundle\Entity\Instance;
use PHPUnit\Framework\TestCase;

final class InstanceCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn(): void
    {
        $this->assertSame(Instance::class, InstanceCrudController::getEntityFqcn());
    }
}