<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Controller\Admin;

use AwsLightsailBundle\Controller\Admin\ContactMethodCrudController;
use AwsLightsailBundle\Entity\ContactMethod;
use PHPUnit\Framework\TestCase;

final class ContactMethodCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn(): void
    {
        $this->assertSame(ContactMethod::class, ContactMethodCrudController::getEntityFqcn());
    }
}