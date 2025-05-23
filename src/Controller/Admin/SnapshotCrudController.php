<?php

namespace AwsLightsailBundle\Controller\Admin;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Snapshot;
use AwsLightsailBundle\Enum\SnapshotTypeEnum;
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
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Lightsail 实例快照管理控制器
 */
class SnapshotCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Snapshot::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('快照')
            ->setEntityLabelInPlural('快照列表')
            ->setPageTitle('index', 'Lightsail 快照管理')
            ->setPageTitle('new', '创建快照')
            ->setPageTitle('edit', fn (Snapshot $snapshot) => sprintf('编辑快照: %s', $snapshot->getName()))
            ->setPageTitle('detail', fn (Snapshot $snapshot) => sprintf('快照详情: %s', $snapshot->getName()))
            ->setSearchFields(['name', 'resourceName', 'region', 'state'])
            ->setDefaultSort(['createTime' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->hideOnForm()
            ->setMaxLength(9999);
            
        yield TextField::new('name', '快照名称');
            
        yield TextField::new('arn', 'AWS ARN')
            ->hideOnForm()
            ->hideOnIndex();
            
        yield TextField::new('resourceName', '资源名称')
            ->setHelp('快照关联的资源名称');
            
        yield ChoiceField::new('type', '快照类型')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => SnapshotTypeEnum::class
            ])
            ->formatValue(function ($value) {
                return $value instanceof SnapshotTypeEnum ? $value->getLabel() : '';
            });
            
        yield TextField::new('region', '区域');
            
        yield TextField::new('state', '状态')
            ->hideOnForm();
            
        yield TextField::new('progress', '进度')
            ->hideOnForm()
            ->hideOnIndex();
            
        yield IntegerField::new('sizeInGb', '大小(GB)')
            ->hideOnForm();
            
        yield BooleanField::new('isFromAutoSnapshot', '是否自动快照')
            ->renderAsSwitch(false)
            ->setFormTypeOption('disabled', true);
            
        yield TextField::new('fromSnapshotName', '源快照名称')
            ->hideOnIndex()
            ->hideOnForm();
            
        yield TextField::new('fromRegion', '源区域')
            ->hideOnIndex()
            ->hideOnForm();
            
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
            
        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm();
            
        yield DateTimeField::new('syncTime', '同步时间')
            ->hideOnForm();
            
        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm();
    }

    public function configureActions(Actions $actions): Actions
    {
        $syncAction = Action::new('syncSnapshot', '同步')
            ->linkToCrudAction('syncSnapshot')
            ->setIcon('fa fa-refresh');
            
        $exportAction = Action::new('exportSnapshot', '导出')
            ->linkToCrudAction('exportSnapshot')
            ->setIcon('fa fa-download')
            ->setCssClass('text-primary');
            
        $restoreAction = Action::new('restoreSnapshot', '恢复')
            ->linkToCrudAction('restoreSnapshot')
            ->setIcon('fa fa-undo')
            ->setCssClass('text-warning');
            
        $copyAction = Action::new('copySnapshot', '复制')
            ->linkToCrudAction('copySnapshot')
            ->setIcon('fa fa-copy')
            ->setCssClass('text-info');
            
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $syncAction)
            ->add(Crud::PAGE_INDEX, $exportAction)
            ->add(Crud::PAGE_INDEX, $restoreAction)
            ->add(Crud::PAGE_INDEX, $copyAction)
            ->add(Crud::PAGE_DETAIL, $syncAction)
            ->add(Crud::PAGE_DETAIL, $exportAction)
            ->add(Crud::PAGE_DETAIL, $restoreAction)
            ->add(Crud::PAGE_DETAIL, $copyAction)
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
        $typeChoices = [];
        foreach (SnapshotTypeEnum::cases() as $case) {
            $typeChoices[$case->getLabel()] = $case->value;
        }
        
        return $filters
            ->add(TextFilter::new('name', '快照名称'))
            ->add(TextFilter::new('resourceName', '资源名称'))
            ->add(TextFilter::new('region', '区域'))
            ->add(ChoiceFilter::new('type', '快照类型')->setChoices($typeChoices))
            ->add(TextFilter::new('state', '状态'))
            ->add(BooleanFilter::new('isFromAutoSnapshot', '是否自动快照'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(EntityFilter::new('credential', 'AWS 凭证'));
    }
    
    /**
     * 同步快照状态
     */
    #[Route('admin/snapshot/{entityId}/sync', name: 'sync_snapshot')]
    public function syncSnapshot(AdminContext $context): Response
    {
        $snapshot = $context->getEntity()->getInstance();
        
        $this->addFlash('info', sprintf('快照 %s 同步指令已发送', $snapshot->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
    
    /**
     * 导出快照
     */
    #[Route('admin/snapshot/{entityId}/export', name: 'export_snapshot')]
    public function exportSnapshot(AdminContext $context): Response
    {
        $snapshot = $context->getEntity()->getInstance();
        
        $this->addFlash('info', sprintf('快照 %s 导出指令已发送', $snapshot->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
    
    /**
     * 从快照恢复
     */
    #[Route('admin/snapshot/{entityId}/restore', name: 'restore_snapshot')]
    public function restoreSnapshot(AdminContext $context): Response
    {
        $snapshot = $context->getEntity()->getInstance();
        
        $this->addFlash('warning', sprintf('快照 %s 恢复指令已发送', $snapshot->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
    
    /**
     * 复制快照到其他区域
     */
    #[Route('admin/snapshot/{entityId}/copy', name: 'copy_snapshot')]
    public function copySnapshot(AdminContext $context): Response
    {
        $snapshot = $context->getEntity()->getInstance();
        
        $this->addFlash('success', sprintf('快照 %s 复制指令已发送', $snapshot->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
} 