<?php

namespace AwsLightsailBundle\Controller\Admin\Action;

use AwsLightsailBundle\Entity\DiskSnapshot;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CopyDiskSnapshotController extends AbstractController
{
    public function __construct(
        private readonly AdminUrlGenerator $adminUrlGenerator
    ) {}

    #[Route('/admin/disk-snapshot/{entityId}/copy', name: 'copy_disk_snapshot')]
    public function __invoke(AdminContext $context): Response
    {
        /** @var DiskSnapshot $snapshot */
        $snapshot = $context->getEntity()->getInstance();

        $this->addFlash('info', sprintf('磁盘快照 %s 复制指令已发送', $snapshot->getName()));

        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
}