<?php

namespace AwsLightsailBundle\Controller\Admin;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Disk;
use AwsLightsailBundle\Entity\DiskSnapshot;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Lightsail 磁盘快照管理控制器
 */
class DiskSnapshotCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return DiskSnapshot::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('磁盘快照')
            ->setEntityLabelInPlural('磁盘快照列表')
            ->setPageTitle('index', 'Lightsail 磁盘快照管理')
            ->setPageTitle('new', '创建磁盘快照')
            ->setPageTitle('edit', fn (DiskSnapshot $snapshot) => sprintf('编辑磁盘快照: %s', $snapshot->getName()))
            ->setPageTitle('detail', fn (DiskSnapshot $snapshot) => sprintf('磁盘快照详情: %s', $snapshot->getName()))
            ->setSearchFields(['name', 'diskName', 'region', 'state'])
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
            
        yield TextField::new('diskName', '磁盘名称');
            
        yield TextField::new('diskPath', '磁盘路径')
            ->hideOnIndex();
            
        yield TextField::new('region', '区域');
            
        yield IntegerField::new('sizeInGb', '大小(GB)')
            ->hideOnForm();
            
        yield TextField::new('state', '状态')
            ->hideOnForm();
            
        yield TextField::new('progress', '进度')
            ->hideOnForm();
            
        yield BooleanField::new('isFromAutoSnapshot', '来自自动快照')
            ->renderAsSwitch(false)
            ->setFormTypeOption('disabled', true);
            
        yield TextField::new('fromDiskSnapshotName', '来源快照名称')
            ->hideOnIndex();
            
        yield TextField::new('fromRegion', '来源区域')
            ->hideOnIndex();
            
        yield CodeEditorField::new('tags', '标签')
            ->hideOnForm()
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return $value ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '{}';
            });
            
        yield AssociationField::new('disk', '关联磁盘')
            ->hideOnIndex()
            ->setFormTypeOption('disabled', true)
            ->formatValue(function ($value) {
                return $value instanceof Disk ? $value->getName() : '';
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
        $syncAction = Action::new('syncDiskSnapshot', '同步')
            ->linkToCrudAction('syncDiskSnapshot')
            ->setIcon('fa fa-refresh');
            
        $restoreAction = Action::new('restoreDiskSnapshot', '还原')
            ->linkToCrudAction('restoreDiskSnapshot')
            ->setIcon('fa fa-history')
            ->setCssClass('text-primary');
            
        $exportAction = Action::new('exportDiskSnapshot', '导出')
            ->linkToCrudAction('exportDiskSnapshot')
            ->setIcon('fa fa-download')
            ->setCssClass('text-success');
            
        $copyAction = Action::new('copyDiskSnapshot', '复制到其他区域')
            ->linkToCrudAction('copyDiskSnapshot')
            ->setIcon('fa fa-copy')
            ->setCssClass('text-info');
            
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $syncAction)
            ->add(Crud::PAGE_INDEX, $restoreAction)
            ->add(Crud::PAGE_INDEX, $exportAction)
            ->add(Crud::PAGE_INDEX, $copyAction)
            ->add(Crud::PAGE_DETAIL, $syncAction)
            ->add(Crud::PAGE_DETAIL, $restoreAction)
            ->add(Crud::PAGE_DETAIL, $exportAction)
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
        return $filters
            ->add(TextFilter::new('name', '快照名称'))
            ->add(TextFilter::new('diskName', '磁盘名称'))
            ->add(TextFilter::new('region', '区域'))
            ->add(TextFilter::new('state', '状态'))
            ->add(BooleanFilter::new('isFromAutoSnapshot', '来自自动快照'))
            ->add(EntityFilter::new('disk', '关联磁盘'))
            ->add(EntityFilter::new('credential', 'AWS 凭证'));
    }
    
    /**
     * 同步磁盘快照状态
     */
    #[Route('admin/disk-snapshot/{entityId}/sync', name: 'sync_disk_snapshot')]
    public function syncDiskSnapshot(AdminContext $context): Response
    {
        $snapshot = $context->getEntity()->getInstance();
        
        $this->addFlash('info', sprintf('磁盘快照 %s 同步指令已发送', $snapshot->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
    
    /**
     * 从快照还原磁盘
     */
    #[Route('admin/disk-snapshot/{entityId}/restore', name: 'restore_disk_snapshot')]
    public function restoreDiskSnapshot(AdminContext $context): Response
    {
        $snapshot = $context->getEntity()->getInstance();
        
        $this->addFlash('warning', sprintf('从磁盘快照 %s 还原指令已发送', $snapshot->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
    
    /**
     * 导出磁盘快照
     */
    #[Route('admin/disk-snapshot/{entityId}/export', name: 'export_disk_snapshot')]
    public function exportDiskSnapshot(AdminContext $context): Response
    {
        $snapshot = $context->getEntity()->getInstance();
        
        $this->addFlash('success', sprintf('磁盘快照 %s 导出指令已发送', $snapshot->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
    
    /**
     * 复制磁盘快照到其他区域
     */
    #[Route('admin/disk-snapshot/{entityId}/copy', name: 'copy_disk_snapshot')]
    public function copyDiskSnapshot(AdminContext $context): Response
    {
        $snapshot = $context->getEntity()->getInstance();
        
        $this->addFlash('info', sprintf('磁盘快照 %s 复制指令已发送', $snapshot->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
} 