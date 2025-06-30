<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Controller\Admin;

use AwsLightsailBundle\Controller\Admin\DiskSnapshotCrudController;
use AwsLightsailBundle\Entity\DiskSnapshot;
use PHPUnit\Framework\TestCase;

final class DiskSnapshotCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn(): void
    {
        $this->assertSame(DiskSnapshot::class, DiskSnapshotCrudController::getEntityFqcn());
    }
}