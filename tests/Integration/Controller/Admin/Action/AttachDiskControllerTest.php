<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Integration\Controller\Admin\Action;

use AwsLightsailBundle\Controller\Admin\Action\AttachDiskController;
use AwsLightsailBundle\Entity\Disk;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class AttachDiskControllerTest extends TestCase
{
    private AttachDiskController $controller;
    private AdminUrlGenerator $adminUrlGenerator;

    protected function setUp(): void
    {
        $this->adminUrlGenerator = $this->createMock(AdminUrlGenerator::class);
        $this->controller = new AttachDiskController($this->adminUrlGenerator);
    }

    public function testController_isInitializedCorrectly(): void
    {
        $this->assertInstanceOf(AttachDiskController::class, $this->controller);
    }


    public function testInvoke_returnsRedirectResponse(): void
    {
        $disk = $this->createMock(Disk::class);
        $disk->method('getName')->willReturn('test-disk');

        $entityDto = $this->createMock(EntityDto::class);
        $entityDto->method('getInstance')->willReturn($disk);

        $context = $this->createMock(AdminContext::class);
        $context->method('getEntity')->willReturn($entityDto);

        $this->adminUrlGenerator
            ->method('setAction')
            ->willReturnSelf();
        $this->adminUrlGenerator
            ->method('setEntityId')
            ->willReturnSelf();
        $this->adminUrlGenerator
            ->method('generateUrl')
            ->willReturn('/admin/dashboard');

        $response = $this->controller->__invoke($context);

        $this->assertInstanceOf(RedirectResponse::class, $response);
    }
}
