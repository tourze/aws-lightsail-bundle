<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Controller\Admin;

use AwsLightsailBundle\Controller\Admin\DatabaseSnapshotCrudController;
use AwsLightsailBundle\Entity\DatabaseSnapshot;
use PHPUnit\Framework\TestCase;

final class DatabaseSnapshotCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn(): void
    {
        $this->assertSame(DatabaseSnapshot::class, DatabaseSnapshotCrudController::getEntityFqcn());
    }
}