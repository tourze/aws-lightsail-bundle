<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Controller\Admin\Action;

use AwsLightsailBundle\Entity\Database;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Autoconfigure(public: true)]
final class StopDatabaseController extends AbstractController
{
    public function __construct(
        private readonly AdminUrlGenerator $adminUrlGenerator,
    ) {
    }

    #[Route(path: '/admin/database/{entityId}/stop', name: 'stop_database', methods: ['POST'])]
    public function __invoke(AdminContext $context): Response
    {
        $database = $context->getEntity()->getInstance();
        \assert($database instanceof Database);

        $this->addFlash('warning', \sprintf('数据库 %s 停止指令已发送', $database->getName()));

        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
}
