<?php

namespace AwsLightsailBundle\Controller\Admin\Action;

use AwsLightsailBundle\Entity\Alarm;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ToggleAlarmNotificationController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    #[Route('/admin/alarm/{entityId}/toggle-notification', name: 'toggle_alarm_notification')]
    public function __invoke(AdminContext $context): Response
    {
        /** @var Alarm $alarm */
        $alarm = $context->getEntity()->getInstance();
        $currentState = $alarm->isNotificationEnabled();
        
        $alarm->setNotificationEnabled(!$currentState);
        $this->entityManager->flush();
        
        $newState = $alarm->isNotificationEnabled() ? '启用' : '禁用';
        $this->addFlash('success', sprintf('告警 %s 通知已%s', $alarm->getName(), $newState));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
}