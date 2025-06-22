<?php

namespace AwsLightsailBundle\Controller\Admin\Action;

use AwsLightsailBundle\Entity\Alarm;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestAlarmController extends AbstractController
{
    public function __construct(
        private readonly AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    #[Route('/admin/alarm/{entityId}/test', name: 'test_alarm')]
    public function __invoke(AdminContext $context): Response
    {
        /** @var Alarm $alarm */
        $alarm = $context->getEntity()->getInstance();
        
        $this->addFlash('warning', sprintf('告警 %s 测试指令已发送', $alarm->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
}