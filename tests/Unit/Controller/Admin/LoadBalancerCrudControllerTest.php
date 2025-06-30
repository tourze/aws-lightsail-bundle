<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Controller\Admin;

use AwsLightsailBundle\Controller\Admin\LoadBalancerCrudController;
use AwsLightsailBundle\Entity\LoadBalancer;
use PHPUnit\Framework\TestCase;

final class LoadBalancerCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn(): void
    {
        $this->assertSame(LoadBalancer::class, LoadBalancerCrudController::getEntityFqcn());
    }
}