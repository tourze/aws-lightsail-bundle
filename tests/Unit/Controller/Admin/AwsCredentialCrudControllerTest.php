<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Controller\Admin;

use AwsLightsailBundle\Controller\Admin\AwsCredentialCrudController;
use AwsLightsailBundle\Entity\AwsCredential;
use PHPUnit\Framework\TestCase;

final class AwsCredentialCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn(): void
    {
        $this->assertSame(AwsCredential::class, AwsCredentialCrudController::getEntityFqcn());
    }
}