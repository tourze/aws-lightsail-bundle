<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Controller\Admin;

use AwsLightsailBundle\Controller\Admin\BucketCrudController;
use AwsLightsailBundle\Entity\Bucket;
use PHPUnit\Framework\TestCase;

final class BucketCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn(): void
    {
        $this->assertSame(Bucket::class, BucketCrudController::getEntityFqcn());
    }
}