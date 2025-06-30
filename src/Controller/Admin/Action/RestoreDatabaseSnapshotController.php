<?php

namespace AwsLightsailBundle\Controller\Admin\Action;

use AwsLightsailBundle\Entity\DatabaseSnapshot;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RestoreDatabaseSnapshotController extends AbstractController
{
    public function __construct(
        private readonly AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    #[Route(path: '/admin/database-snapshot/{entityId}/restore', name: 'restore_database_snapshot')]
    public function __invoke(AdminContext $context): Response
    {
        /** @var DatabaseSnapshot $snapshot */
        $snapshot = $context->getEntity()->getInstance();
        
        $this->addFlash('warning', sprintf('从数据库快照 %s 还原指令已发送', $snapshot->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
}