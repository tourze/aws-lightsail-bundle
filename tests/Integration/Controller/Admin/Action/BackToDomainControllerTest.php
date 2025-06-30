<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Integration\Controller\Admin\Action;

use AwsLightsailBundle\Controller\Admin\Action\BackToDomainController;
use AwsLightsailBundle\Entity\Domain;
use AwsLightsailBundle\Entity\DomainEntry;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class BackToDomainControllerTest extends TestCase
{
    private BackToDomainController $controller;
    private AdminUrlGenerator $adminUrlGenerator;

    protected function setUp(): void
    {
        $this->adminUrlGenerator = $this->createMock(AdminUrlGenerator::class);
        $this->controller = new BackToDomainController($this->adminUrlGenerator);
    }

    public function testController_isInitializedCorrectly(): void
    {
        $this->assertInstanceOf(BackToDomainController::class, $this->controller);
    }


    public function testInvoke_withDomainEntry_redirectsToDomainDetail(): void
    {
        $domain = $this->createMock(Domain::class);
        $domain->method('getId')->willReturn(123);

        $domainEntry = $this->createMock(DomainEntry::class);
        $domainEntry->method('getDomain')->willReturn($domain);

        $entityDto = $this->createMock(EntityDto::class);
        $entityDto->method('getInstance')->willReturn($domainEntry);

        $context = $this->createMock(AdminContext::class);
        $context->method('getEntity')->willReturn($entityDto);

        $this->adminUrlGenerator
            ->method('setController')
            ->willReturnSelf();
        $this->adminUrlGenerator
            ->method('setAction')
            ->willReturnSelf();
        $this->adminUrlGenerator
            ->method('setEntityId')
            ->willReturnSelf();
        $this->adminUrlGenerator
            ->method('generateUrl')
            ->willReturn('/admin/domain/123');

        $response = $this->controller->__invoke($context);

        $this->assertInstanceOf(RedirectResponse::class, $response);
    }
}
