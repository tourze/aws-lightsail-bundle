<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Controller\Admin;

use AwsLightsailBundle\Controller\Admin\DistributionCrudController;
use AwsLightsailBundle\Entity\Distribution;
use PHPUnit\Framework\TestCase;

final class DistributionCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn(): void
    {
        $this->assertSame(Distribution::class, DistributionCrudController::getEntityFqcn());
    }
}