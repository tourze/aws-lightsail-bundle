<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Integration\Service;

use AwsLightsailBundle\Service\KeyPairSyncService;
use PHPUnit\Framework\TestCase;

final class KeyPairSyncServiceTest extends TestCase
{
    public function testService_classExists(): void
    {
        $this->assertTrue(class_exists(KeyPairSyncService::class));
    }}