<?php

namespace AwsLightsailBundle\Controller\Admin;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Operation;
use AwsLightsailBundle\Enum\OperationStatusEnum;
use AwsLightsailBundle\Enum\OperationTypeEnum;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Lightsail 操作记录管理控制器
 */
class OperationCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Operation::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('操作记录')
            ->setEntityLabelInPlural('操作记录列表')
            ->setPageTitle('index', 'Lightsail 操作记录管理')
            ->setPageTitle('detail', fn (Operation $operation) => sprintf('操作记录详情: %s', $operation->getOperationId()))
            ->setSearchFields(['operationId', 'resourceName', 'resourceType', 'errorCode'])
            ->setDefaultSort(['createTime' => 'DESC'])
            ->showEntityActionsInlined()
            ->setTimezone('Asia/Shanghai');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->hideOnForm()
            ->setMaxLength(9999);
            
        yield TextField::new('operationId', '操作 ID');
            
        yield TextField::new('resourceName', '资源名称');
            
        yield TextField::new('resourceType', '资源类型');
            
        yield ChoiceField::new('type', '操作类型')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => OperationTypeEnum::class
            ])
            ->formatValue(function ($value) {
                return $value instanceof OperationTypeEnum ? $value->getLabel() : '';
            });
            
        yield ChoiceField::new('status', '状态')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => OperationStatusEnum::class
            ])
            ->formatValue(function ($value) {
                return $value instanceof OperationStatusEnum ? $value->getLabel() : '';
            });
            
        yield TextField::new('region', '区域');
            
        yield TextField::new('errorCode', '错误代码')
            ->hideOnIndex();
            
        yield TextField::new('errorDetails', '错误详情')
            ->hideOnIndex();
            
        yield CodeEditorField::new('metadata', '元数据')
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return $value ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '{}';
            });
            
        yield AssociationField::new('credential', 'AWS 凭证')
            ->formatValue(function ($value) {
                return $value instanceof AwsCredential ? $value->getName() : '';
            });
            
        yield DateTimeField::new('createTime', '创建时间');
            
        yield DateTimeField::new('completeTime', '完成时间')
            ->hideOnIndex();
            
        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnIndex();
    }

    public function configureActions(Actions $actions): Actions
    {
        $refreshAction = Action::new('refreshOperation', '刷新状态')
            ->linkToCrudAction('refreshOperation')
            ->setIcon('fa fa-refresh');
            
        $viewResourceAction = Action::new('viewResource', '查看资源')
            ->linkToCrudAction('viewResource')
            ->setIcon('fa fa-eye')
            ->setCssClass('text-primary');
            
        return $actions
            ->disable(Action::NEW, Action::EDIT)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $refreshAction)
            ->add(Crud::PAGE_INDEX, $viewResourceAction)
            ->add(Crud::PAGE_DETAIL, $refreshAction)
            ->add(Crud::PAGE_DETAIL, $viewResourceAction)
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->setIcon('fa fa-trash')->setLabel('删除');
            })
            ->update(Crud::PAGE_INDEX, Action::DETAIL, function (Action $action) {
                return $action->setIcon('fa fa-eye')->setLabel('查看');
            });
    }
    
    public function configureFilters(Filters $filters): Filters
    {
        $typeChoices = [];
        foreach (OperationTypeEnum::cases() as $case) {
            $typeChoices[$case->getLabel()] = $case->value;
        }
        
        $statusChoices = [];
        foreach (OperationStatusEnum::cases() as $case) {
            $statusChoices[$case->getLabel()] = $case->value;
        }
        
        return $filters
            ->add(TextFilter::new('operationId', '操作 ID'))
            ->add(TextFilter::new('resourceName', '资源名称'))
            ->add(TextFilter::new('resourceType', '资源类型'))
            ->add(ChoiceFilter::new('type', '操作类型')->setChoices($typeChoices))
            ->add(ChoiceFilter::new('status', '状态')->setChoices($statusChoices))
            ->add(TextFilter::new('region', '区域'))
            ->add(TextFilter::new('errorCode', '错误代码'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('completeTime', '完成时间'))
            ->add(EntityFilter::new('credential', 'AWS 凭证'));
    }
    
    /**
     * 刷新操作状态
     */
    #[Route('admin/operation/{entityId}/refresh', name: 'refresh_operation')]
    public function refreshOperation(AdminContext $context): Response
    {
        $operation = $context->getEntity()->getInstance();
        
        $this->addFlash('info', sprintf('操作记录 %s 刷新指令已发送', $operation->getOperationId()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
    
    /**
     * 查看相关资源
     */
    #[Route('admin/operation/{entityId}/view-resource', name: 'view_operation_resource')]
    public function viewResource(AdminContext $context): Response
    {
        $operation = $context->getEntity()->getInstance();
        $resourceName = $operation->getResourceName();
        $resourceType = $operation->getResourceType();
        
        if (!$resourceName || !$resourceType) {
            $this->addFlash('warning', '该操作没有关联的资源信息');
            
            return $this->redirect($this->adminUrlGenerator
                ->setAction(Action::INDEX)
                ->setEntityId(null)
                ->generateUrl());
        }
        
        // 根据资源类型决定重定向到哪个控制器
        $controllerClass = match ($resourceType) {
            'Instance' => InstanceCrudController::class,
            'LoadBalancer' => LoadBalancerCrudController::class,
            'Domain' => DomainCrudController::class,
            'Database' => DatabaseCrudController::class,
            'Disk' => DiskCrudController::class,
            'Certificate' => CertificateCrudController::class,
            'Distribution' => DistributionCrudController::class,
            'Bucket' => BucketCrudController::class,
            'KeyPair' => KeyPairCrudController::class,
            default => null
        };
        
        if (!$controllerClass) {
            $this->addFlash('warning', sprintf('无法识别资源类型: %s', $resourceType));
            
            return $this->redirect($this->adminUrlGenerator
                ->setAction(Action::INDEX)
                ->setEntityId(null)
                ->generateUrl());
        }
        
        // 尝试查找对应的资源实体
        $repository = $this->entityManager->getRepository($controllerClass::getEntityFqcn());
        $resource = $repository->findOneBy(['name' => $resourceName]);
        
        if (!$resource) {
            $this->addFlash('warning', sprintf('找不到资源: %s [%s]', $resourceName, $resourceType));
            
            return $this->redirect($this->adminUrlGenerator
                ->setAction(Action::INDEX)
                ->setEntityId(null)
                ->generateUrl());
        }
        
        // 重定向到资源详情页
        return $this->redirect($this->adminUrlGenerator
            ->setController($controllerClass)
            ->setAction(Action::DETAIL)
            ->setEntityId($resource->getId())
            ->generateUrl());
    }
} 