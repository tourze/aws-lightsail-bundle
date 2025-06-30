<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Controller\Admin;

use AwsLightsailBundle\Controller\Admin\DomainEntryCrudController;
use AwsLightsailBundle\Entity\DomainEntry;
use PHPUnit\Framework\TestCase;

final class DomainEntryCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn(): void
    {
        $this->assertSame(DomainEntry::class, DomainEntryCrudController::getEntityFqcn());
    }
}