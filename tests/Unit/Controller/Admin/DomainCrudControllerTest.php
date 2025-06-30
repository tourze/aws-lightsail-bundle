<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Controller\Admin;

use AwsLightsailBundle\Controller\Admin\DomainCrudController;
use AwsLightsailBundle\Entity\Domain;
use PHPUnit\Framework\TestCase;

final class DomainCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn(): void
    {
        $this->assertSame(Domain::class, DomainCrudController::getEntityFqcn());
    }
}