<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Controller\Admin;

use AwsLightsailBundle\Controller\Admin\DiskCrudController;
use AwsLightsailBundle\Entity\Disk;
use PHPUnit\Framework\TestCase;

final class DiskCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn(): void
    {
        $this->assertSame(Disk::class, DiskCrudController::getEntityFqcn());
    }
}