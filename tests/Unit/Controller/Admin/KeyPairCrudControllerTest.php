<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Controller\Admin;

use AwsLightsailBundle\Controller\Admin\KeyPairCrudController;
use AwsLightsailBundle\Entity\KeyPair;
use PHPUnit\Framework\TestCase;

final class KeyPairCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn(): void
    {
        $this->assertSame(KeyPair::class, KeyPairCrudController::getEntityFqcn());
    }
}