<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Controller\Admin;

use AwsLightsailBundle\Controller\Admin\SnapshotCrudController;
use AwsLightsailBundle\Entity\Snapshot;
use PHPUnit\Framework\TestCase;

final class SnapshotCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn(): void
    {
        $this->assertSame(Snapshot::class, SnapshotCrudController::getEntityFqcn());
    }
}