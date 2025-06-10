<?php

namespace AwsLightsailBundle\Controller\Admin;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\ContainerService;
use AwsLightsailBundle\Enum\ContainerServicePowerEnum;
use AwsLightsailBundle\Enum\ContainerServiceStateEnum;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Lightsail 容器服务管理控制器
 */
class ContainerServiceCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return ContainerService::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('容器服务')
            ->setEntityLabelInPlural('容器服务列表')
            ->setPageTitle('index', 'Lightsail 容器服务管理')
            ->setPageTitle('new', '创建容器服务')
            ->setPageTitle('edit', fn (ContainerService $service) => sprintf('编辑容器服务: %s', $service->getName()))
            ->setPageTitle('detail', fn (ContainerService $service) => sprintf('容器服务详情: %s', $service->getName()))
            ->setSearchFields(['name', 'url', 'region'])
            ->setDefaultSort(['name' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->hideOnForm()
            ->setMaxLength(9999);
            
        yield TextField::new('name', '服务名称');
            
        yield TextField::new('arn', 'AWS ARN')
            ->hideOnForm()
            ->hideOnIndex();
            
        yield ChoiceField::new('power', '计算能力')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => ContainerServicePowerEnum::class
            ])
            ->formatValue(function ($value) {
                return $value instanceof ContainerServicePowerEnum ? $value->getLabel() : '';
            });
            
        yield IntegerField::new('scale', '规模')
            ->setHelp('服务的节点数');
            
        yield ChoiceField::new('state', '状态')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => ContainerServiceStateEnum::class
            ])
            ->formatValue(function ($value) {
                return $value instanceof ContainerServiceStateEnum ? $value->getLabel() : '';
            });
            
        yield TextField::new('region', '区域');
            
        yield TextField::new('url', '服务 URL')
            ->hideOnForm();
            
        yield CodeEditorField::new('currentDeployment', '当前部署')
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return $value ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '{}';
            });
            
        yield CodeEditorField::new('nextDeployment', '下一个部署')
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return $value ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '{}';
            });
            
        yield BooleanField::new('isPublicDomainEnabled', '公共域名已启用')
            ->renderAsSwitch(true);
            
        yield BooleanField::new('isPrivateDomainEnabled', '私有域名已启用')
            ->renderAsSwitch(true);
            
        yield CodeEditorField::new('privateDomainName', '私有域名')
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return $value ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '{}';
            });
            
        yield TextField::new('publicDomainNames', '公共域名')
            ->hideOnIndex();
            
        yield CodeEditorField::new('containerImages', '容器镜像')
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return $value ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '[]';
            });
            
        yield CodeEditorField::new('tags', '标签')
            ->hideOnForm()
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return $value ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '{}';
            });
            
        yield AssociationField::new('credential', 'AWS 凭证')
            ->setFormTypeOption('disabled', $pageName !== Crud::PAGE_NEW)
            ->formatValue(function ($value) {
                return $value instanceof AwsCredential ? $value->getName() : '';
            });
            
        yield DateTimeField::new('createdAt', '创建时间')
            ->hideOnForm();
            
        yield DateTimeField::new('syncedAt', '同步时间')
            ->hideOnForm();
            
        yield DateTimeField::new('updatedAt', '更新时间')
            ->hideOnForm();
    }

    public function configureActions(Actions $actions): Actions
    {
        $syncAction = Action::new('syncContainerService', '同步')
            ->linkToCrudAction('syncContainerService')
            ->setIcon('fa fa-refresh');
            
        $deployAction = Action::new('deployContainerService', '部署')
            ->linkToCrudAction('deployContainerService')
            ->setIcon('fa fa-cloud-upload')
            ->setCssClass('text-primary');
            
        $restartAction = Action::new('restartContainerService', '重启')
            ->linkToCrudAction('restartContainerService')
            ->setIcon('fa fa-power-off')
            ->setCssClass('text-warning');
            
        $registerImageAction = Action::new('registerImage', '注册镜像')
            ->linkToCrudAction('registerImage')
            ->setIcon('fa fa-docker')
            ->setCssClass('text-success');
            
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $syncAction)
            ->add(Crud::PAGE_INDEX, $deployAction)
            ->add(Crud::PAGE_INDEX, $restartAction)
            ->add(Crud::PAGE_INDEX, $registerImageAction)
            ->add(Crud::PAGE_DETAIL, $syncAction)
            ->add(Crud::PAGE_DETAIL, $deployAction)
            ->add(Crud::PAGE_DETAIL, $restartAction)
            ->add(Crud::PAGE_DETAIL, $registerImageAction)
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
        $powerChoices = [];
        foreach (ContainerServicePowerEnum::cases() as $case) {
            $powerChoices[$case->getLabel()] = $case->value;
        }
        
        $stateChoices = [];
        foreach (ContainerServiceStateEnum::cases() as $case) {
            $stateChoices[$case->getLabel()] = $case->value;
        }
        
        return $filters
            ->add(TextFilter::new('name', '服务名称'))
            ->add(TextFilter::new('url', '服务 URL'))
            ->add(TextFilter::new('region', '区域'))
            ->add(ChoiceFilter::new('power', '计算能力')->setChoices($powerChoices))
            ->add(ChoiceFilter::new('state', '状态')->setChoices($stateChoices))
            ->add(BooleanFilter::new('isPublicDomainEnabled', '公共域名已启用'))
            ->add(BooleanFilter::new('isPrivateDomainEnabled', '私有域名已启用'))
            ->add(EntityFilter::new('credential', 'AWS 凭证'));
    }
    
    /**
     * 同步容器服务状态
     */
    #[Route('admin/container-service/{entityId}/sync', name: 'sync_container_service')]
    public function syncContainerService(AdminContext $context): Response
    {
        $service = $context->getEntity()->getInstance();
        
        $this->addFlash('info', sprintf('容器服务 %s 同步指令已发送', $service->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
    
    /**
     * 部署容器服务
     */
    #[Route('admin/container-service/{entityId}/deploy', name: 'deploy_container_service')]
    public function deployContainerService(AdminContext $context): Response
    {
        $service = $context->getEntity()->getInstance();
        
        $this->addFlash('warning', sprintf('容器服务 %s 部署指令已发送', $service->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
    
    /**
     * 重启容器服务
     */
    #[Route('admin/container-service/{entityId}/restart', name: 'restart_container_service')]
    public function restartContainerService(AdminContext $context): Response
    {
        $service = $context->getEntity()->getInstance();
        
        $this->addFlash('warning', sprintf('容器服务 %s 重启指令已发送', $service->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
    
    /**
     * 注册镜像
     */
    #[Route('admin/container-service/{entityId}/register-image', name: 'register_container_image')]
    public function registerImage(AdminContext $context): Response
    {
        $service = $context->getEntity()->getInstance();
        
        $this->addFlash('success', sprintf('容器服务 %s 注册镜像指令已发送', $service->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
}
