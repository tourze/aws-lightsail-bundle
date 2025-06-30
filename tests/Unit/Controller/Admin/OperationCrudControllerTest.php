<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Controller\Admin;

use AwsLightsailBundle\Controller\Admin\OperationCrudController;
use AwsLightsailBundle\Entity\Operation;
use PHPUnit\Framework\TestCase;

final class OperationCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn(): void
    {
        $this->assertSame(Operation::class, OperationCrudController::getEntityFqcn());
    }
}