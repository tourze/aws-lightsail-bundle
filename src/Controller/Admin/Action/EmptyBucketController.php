<?php

namespace AwsLightsailBundle\Controller\Admin\Action;

use AwsLightsailBundle\Entity\Bucket;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EmptyBucketController extends AbstractController
{
    public function __construct(
        private readonly AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    #[Route('/admin/bucket/{entityId}/empty', name: 'empty_bucket')]
    public function __invoke(AdminContext $context): Response
    {
        /** @var Bucket $bucket */
        $bucket = $context->getEntity()->getInstance();
        
        $this->addFlash('warning', sprintf('存储桶 %s 清空指令已发送', $bucket->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
}