<?php

namespace AwsLightsailBundle\Controller\Admin\Action;

use AwsLightsailBundle\Controller\Admin\DomainCrudController;
use AwsLightsailBundle\Entity\DomainEntry;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BackToDomainController extends AbstractController
{
    public function __construct(
        private readonly AdminUrlGenerator $adminUrlGenerator
    ) {}

    #[Route('/admin/domain-entry/back-to-domain', name: 'back_to_domain_entry')]
    public function __invoke(AdminContext $context): Response
    {
        // 如果是实体上下文，获取关联的域名
        $entity = $context->getEntity();
        if ($entity->getInstance() instanceof DomainEntry) {
            $entry = $entity->getInstance();
            $domain = $entry->getDomain();

            return $this->redirect($this->adminUrlGenerator
                ->setController(DomainCrudController::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($domain->getId())
                ->generateUrl());
        }

        // 否则返回到域名列表
        return $this->redirect($this->adminUrlGenerator
            ->setController(DomainCrudController::class)
            ->setAction(Action::INDEX)
            ->generateUrl());
    }
}
