<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Controller\Admin;

use AwsLightsailBundle\Controller\Admin\DatabaseCrudController;
use AwsLightsailBundle\Entity\Database;
use PHPUnit\Framework\TestCase;

final class DatabaseCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn(): void
    {
        $this->assertSame(Database::class, DatabaseCrudController::getEntityFqcn());
    }
}