<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Controller\Admin\Action;

use AwsLightsailBundle\Entity\Disk;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Autoconfigure(public: true)]
final class AttachDiskController extends AbstractController
{
    public function __construct(
        private readonly AdminUrlGenerator $adminUrlGenerator,
    ) {
    }

    #[Route(path: '/admin/disk/{entityId}/attach', name: 'attach_disk', methods: ['POST'])]
    public function __invoke(AdminContext $context): Response
    {
        $disk = $context->getEntity()->getInstance();
        \assert($disk instanceof Disk);

        $this->addFlash('info', \sprintf('磁盘 %s 挂载指令已发送', $disk->getName()));

        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
}
