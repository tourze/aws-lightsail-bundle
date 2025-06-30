<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Controller\Admin;

use AwsLightsailBundle\Controller\Admin\CertificateCrudController;
use AwsLightsailBundle\Entity\Certificate;
use PHPUnit\Framework\TestCase;

final class CertificateCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn(): void
    {
        $this->assertSame(Certificate::class, CertificateCrudController::getEntityFqcn());
    }
}