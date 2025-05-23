<?php

namespace AwsLightsailBundle\Controller\Admin;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\StaticIp;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Lightsail 静态 IP 管理控制器
 */
class StaticIpCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return StaticIp::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('静态 IP')
            ->setEntityLabelInPlural('静态 IP 列表')
            ->setPageTitle('index', 'Lightsail 静态 IP 管理')
            ->setPageTitle('new', '创建静态 IP')
            ->setPageTitle('edit', fn (StaticIp $staticIp) => sprintf('编辑静态 IP: %s', $staticIp->getName()))
            ->setPageTitle('detail', fn (StaticIp $staticIp) => sprintf('静态 IP 详情: %s', $staticIp->getName()))
            ->setSearchFields(['name', 'ipAddress', 'attachedTo', 'region'])
            ->setDefaultSort(['name' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->hideOnForm()
            ->setMaxLength(9999);
            
        yield TextField::new('name', '名称');
            
        yield TextField::new('arn', 'AWS ARN')
            ->hideOnForm()
            ->hideOnIndex();
            
        yield TextField::new('ipAddress', 'IP 地址');
            
        yield TextField::new('attachedTo', '附加到')
            ->setHelp('静态 IP 附加到的实例名称')
            ->setFormTypeOption('disabled', true);
            
        yield BooleanField::new('isAttached', '是否已附加')
            ->renderAsSwitch(false)
            ->setFormTypeOption('disabled', true);
            
        yield TextField::new('region', '区域');
            
        yield AssociationField::new('credential', 'AWS 凭证')
            ->setFormTypeOption('disabled', $pageName !== Crud::PAGE_NEW)
            ->formatValue(function ($value) {
                return $value instanceof AwsCredential ? $value->getName() : '';
            });
            
        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm();
            
        yield DateTimeField::new('syncTime', '同步时间')
            ->hideOnForm();
            
        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm();
    }

    public function configureActions(Actions $actions): Actions
    {
        $syncAction = Action::new('syncStaticIp', '同步')
            ->linkToCrudAction('syncStaticIp')
            ->setIcon('fa fa-refresh');
            
        $attachAction = Action::new('attachStaticIp', '附加到实例')
            ->linkToCrudAction('attachStaticIp')
            ->setIcon('fa fa-link')
            ->setCssClass('text-success');
            
        $detachAction = Action::new('detachStaticIp', '分离')
            ->linkToCrudAction('detachStaticIp')
            ->setIcon('fa fa-unlink')
            ->setCssClass('text-warning');
            
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $syncAction)
            ->add(Crud::PAGE_INDEX, $attachAction)
            ->add(Crud::PAGE_INDEX, $detachAction)
            ->add(Crud::PAGE_DETAIL, $syncAction)
            ->add(Crud::PAGE_DETAIL, $attachAction)
            ->add(Crud::PAGE_DETAIL, $detachAction)
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->setIcon('fa fa-trash')->setLabel('删除');
            })
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action->setIcon('fa fa-edit')->setLabel('编辑');
            })
            ->update(Crud::PAGE_INDEX, Action::DETAIL, function (Action $action) {
                return $action->setIcon('fa fa-eye')->setLabel('查看');
            });
    }
    
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('name', '名称'))
            ->add(TextFilter::new('ipAddress', 'IP 地址'))
            ->add(TextFilter::new('attachedTo', '附加到'))
            ->add(TextFilter::new('region', '区域'))
            ->add(BooleanFilter::new('isAttached', '是否已附加'))
            ->add(EntityFilter::new('credential', 'AWS 凭证'));
    }
    
    /**
     * 同步静态 IP 状态
     */
    #[Route('admin/static-ip/{entityId}/sync', name: 'sync_static_ip')]
    public function syncStaticIp(AdminContext $context): Response
    {
        $staticIp = $context->getEntity()->getInstance();
        
        $this->addFlash('info', sprintf('静态 IP %s 同步指令已发送', $staticIp->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
    
    /**
     * 附加静态 IP 到实例
     */
    #[Route('admin/static-ip/{entityId}/attach', name: 'attach_static_ip')]
    public function attachStaticIp(AdminContext $context): Response
    {
        $staticIp = $context->getEntity()->getInstance();
        
        if ($staticIp->isAttached()) {
            $this->addFlash('warning', sprintf('静态 IP %s 已经附加到实例 %s', $staticIp->getName(), $staticIp->getAttachedTo()));
            
            return $this->redirect($this->adminUrlGenerator
                ->setAction(Action::INDEX)
                ->setEntityId(null)
                ->generateUrl());
        }
        
        $this->addFlash('success', sprintf('静态 IP %s 附加指令已发送', $staticIp->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
    
    /**
     * 分离静态 IP
     */
    #[Route('admin/static-ip/{entityId}/detach', name: 'detach_static_ip')]
    public function detachStaticIp(AdminContext $context): Response
    {
        $staticIp = $context->getEntity()->getInstance();
        
        if (!$staticIp->isAttached()) {
            $this->addFlash('warning', sprintf('静态 IP %s 当前未附加到任何实例', $staticIp->getName()));
            
            return $this->redirect($this->adminUrlGenerator
                ->setAction(Action::INDEX)
                ->setEntityId(null)
                ->generateUrl());
        }
        
        $this->addFlash('warning', sprintf('静态 IP %s 分离指令已发送', $staticIp->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
} 