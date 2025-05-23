<?php

namespace AwsLightsailBundle\Controller\Admin;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Disk;
use AwsLightsailBundle\Enum\DiskStateEnum;
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
use Symfony\Component\Routing\Annotation\Route;

/**
 * Lightsail 磁盘管理控制器
 */
class DiskCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Disk::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('磁盘')
            ->setEntityLabelInPlural('磁盘列表')
            ->setPageTitle('index', 'Lightsail 磁盘管理')
            ->setPageTitle('new', '创建磁盘')
            ->setPageTitle('edit', fn (Disk $disk) => sprintf('编辑磁盘: %s', $disk->getName()))
            ->setPageTitle('detail', fn (Disk $disk) => sprintf('磁盘详情: %s', $disk->getName()))
            ->setSearchFields(['name', 'attachedTo', 'region', 'path'])
            ->setDefaultSort(['name' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->hideOnForm()
            ->setMaxLength(9999);
            
        yield TextField::new('name', '磁盘名称');
            
        yield TextField::new('arn', 'AWS ARN')
            ->hideOnForm()
            ->hideOnIndex();
            
        yield TextField::new('attachedTo', '挂载到实例')
            ->setHelp('挂载到的实例名称')
            ->setFormTypeOption('disabled', true);
            
        yield TextField::new('attachmentState', '挂载状态')
            ->hideOnForm();
            
        yield BooleanField::new('isSystemDisk', '系统磁盘')
            ->renderAsSwitch(false)
            ->setFormTypeOption('disabled', true);
            
        yield ChoiceField::new('state', '状态')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => DiskStateEnum::class
            ])
            ->formatValue(function ($value) {
                return $value instanceof DiskStateEnum ? $value->getLabel() : '';
            });
            
        yield TextField::new('region', '区域');
            
        yield IntegerField::new('sizeInGb', '大小(GB)');
            
        yield IntegerField::new('iops', 'IOPS')
            ->hideOnIndex();
            
        yield TextField::new('path', '路径')
            ->hideOnIndex();
            
        yield CodeEditorField::new('tags', '标签')
            ->hideOnForm()
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return $value ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '{}';
            });
            
        yield BooleanField::new('isAutoSnapshotConfigured', '已配置自动快照')
            ->renderAsSwitch(true);
            
        yield TextField::new('supportCode', '支持代码')
            ->hideOnForm()
            ->hideOnIndex();
            
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
        $syncAction = Action::new('syncDisk', '同步')
            ->linkToCrudAction('syncDisk')
            ->setIcon('fa fa-refresh');
            
        $attachAction = Action::new('attachDisk', '挂载')
            ->linkToCrudAction('attachDisk')
            ->setIcon('fa fa-plug')
            ->setCssClass('text-success');
            
        $detachAction = Action::new('detachDisk', '分离')
            ->linkToCrudAction('detachDisk')
            ->setIcon('fa fa-unlink')
            ->setCssClass('text-warning');
            
        $createSnapshotAction = Action::new('createDiskSnapshot', '创建快照')
            ->linkToCrudAction('createDiskSnapshot')
            ->setIcon('fa fa-camera')
            ->setCssClass('text-primary');
            
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $syncAction)
            ->add(Crud::PAGE_INDEX, $attachAction)
            ->add(Crud::PAGE_INDEX, $detachAction)
            ->add(Crud::PAGE_INDEX, $createSnapshotAction)
            ->add(Crud::PAGE_DETAIL, $syncAction)
            ->add(Crud::PAGE_DETAIL, $attachAction)
            ->add(Crud::PAGE_DETAIL, $detachAction)
            ->add(Crud::PAGE_DETAIL, $createSnapshotAction)
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
        $stateChoices = [];
        foreach (DiskStateEnum::cases() as $case) {
            $stateChoices[$case->getLabel()] = $case->value;
        }
        
        return $filters
            ->add(TextFilter::new('name', '磁盘名称'))
            ->add(TextFilter::new('attachedTo', '挂载到实例'))
            ->add(TextFilter::new('region', '区域'))
            ->add(TextFilter::new('path', '路径'))
            ->add(ChoiceFilter::new('state', '状态')->setChoices($stateChoices))
            ->add(BooleanFilter::new('isSystemDisk', '系统磁盘'))
            ->add(BooleanFilter::new('isAutoSnapshotConfigured', '已配置自动快照'))
            ->add(EntityFilter::new('credential', 'AWS 凭证'));
    }
    
    /**
     * 同步磁盘状态
     */
    #[Route('admin/disk/{entityId}/sync', name: 'sync_disk')]
    public function syncDisk(AdminContext $context): Response
    {
        $disk = $context->getEntity()->getInstance();
        
        $this->addFlash('info', sprintf('磁盘 %s 同步指令已发送', $disk->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
    
    /**
     * 挂载磁盘到实例
     */
    #[Route('admin/disk/{entityId}/attach', name: 'attach_disk')]
    public function attachDisk(AdminContext $context): Response
    {
        $disk = $context->getEntity()->getInstance();
        
        $this->addFlash('warning', sprintf('磁盘 %s 挂载指令已发送', $disk->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
    
    /**
     * 从实例分离磁盘
     */
    #[Route('admin/disk/{entityId}/detach', name: 'detach_disk')]
    public function detachDisk(AdminContext $context): Response
    {
        $disk = $context->getEntity()->getInstance();
        
        $this->addFlash('warning', sprintf('磁盘 %s 分离指令已发送', $disk->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
    
    /**
     * 创建磁盘快照
     */
    #[Route('admin/disk/{entityId}/create-snapshot', name: 'create_disk_snapshot')]
    public function createDiskSnapshot(AdminContext $context): Response
    {
        $disk = $context->getEntity()->getInstance();
        
        $this->addFlash('success', sprintf('磁盘 %s 创建快照指令已发送', $disk->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
} 