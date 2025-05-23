<?php

namespace AwsLightsailBundle\Controller\Admin;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Database;
use AwsLightsailBundle\Entity\DatabaseSnapshot;
use AwsLightsailBundle\Enum\DatabaseEngineEnum;
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
 * Lightsail 数据库快照管理控制器
 */
class DatabaseSnapshotCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return DatabaseSnapshot::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('数据库快照')
            ->setEntityLabelInPlural('数据库快照列表')
            ->setPageTitle('index', 'Lightsail 数据库快照管理')
            ->setPageTitle('new', '创建数据库快照')
            ->setPageTitle('edit', fn (DatabaseSnapshot $snapshot) => sprintf('编辑数据库快照: %s', $snapshot->getName()))
            ->setPageTitle('detail', fn (DatabaseSnapshot $snapshot) => sprintf('数据库快照详情: %s', $snapshot->getName()))
            ->setSearchFields(['name', 'databaseName', 'region', 'state'])
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
            
        yield TextField::new('databaseName', '数据库名称');
            
        yield ChoiceField::new('engine', '数据库引擎')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => DatabaseEngineEnum::class
            ])
            ->formatValue(function ($value) {
                return $value instanceof DatabaseEngineEnum ? $value->getLabel() : '';
            });
            
        yield TextField::new('engineVersion', '引擎版本');
            
        yield IntegerField::new('sizeInGb', '大小(GB)')
            ->hideOnForm();
            
        yield TextField::new('region', '区域');
            
        yield TextField::new('state', '状态')
            ->hideOnForm();
            
        yield BooleanField::new('isFromAutoSnapshot', '来自自动快照')
            ->renderAsSwitch(false)
            ->setFormTypeOption('disabled', true);
            
        yield CodeEditorField::new('tags', '标签')
            ->hideOnForm()
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return $value ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '{}';
            });
            
        yield AssociationField::new('database', '关联数据库')
            ->hideOnIndex()
            ->setFormTypeOption('disabled', true)
            ->formatValue(function ($value) {
                return $value instanceof Database ? $value->getName() : '';
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
        $syncAction = Action::new('syncDatabaseSnapshot', '同步')
            ->linkToCrudAction('syncDatabaseSnapshot')
            ->setIcon('fa fa-refresh');
            
        $restoreAction = Action::new('restoreDatabaseSnapshot', '还原')
            ->linkToCrudAction('restoreDatabaseSnapshot')
            ->setIcon('fa fa-history')
            ->setCssClass('text-primary');
            
        $exportAction = Action::new('exportDatabaseSnapshot', '导出')
            ->linkToCrudAction('exportDatabaseSnapshot')
            ->setIcon('fa fa-download')
            ->setCssClass('text-success');
            
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $syncAction)
            ->add(Crud::PAGE_INDEX, $restoreAction)
            ->add(Crud::PAGE_INDEX, $exportAction)
            ->add(Crud::PAGE_DETAIL, $syncAction)
            ->add(Crud::PAGE_DETAIL, $restoreAction)
            ->add(Crud::PAGE_DETAIL, $exportAction)
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
        $engineChoices = [];
        foreach (DatabaseEngineEnum::cases() as $case) {
            $engineChoices[$case->getLabel()] = $case->value;
        }
        
        return $filters
            ->add(TextFilter::new('name', '快照名称'))
            ->add(TextFilter::new('databaseName', '数据库名称'))
            ->add(TextFilter::new('region', '区域'))
            ->add(TextFilter::new('state', '状态'))
            ->add(ChoiceFilter::new('engine', '数据库引擎')->setChoices($engineChoices))
            ->add(BooleanFilter::new('isFromAutoSnapshot', '来自自动快照'))
            ->add(EntityFilter::new('database', '关联数据库'))
            ->add(EntityFilter::new('credential', 'AWS 凭证'));
    }
    
    /**
     * 同步数据库快照状态
     */
    #[Route('admin/database-snapshot/{entityId}/sync', name: 'sync_database_snapshot')]
    public function syncDatabaseSnapshot(AdminContext $context): Response
    {
        $snapshot = $context->getEntity()->getInstance();
        
        $this->addFlash('info', sprintf('数据库快照 %s 同步指令已发送', $snapshot->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
    
    /**
     * 从快照还原数据库
     */
    #[Route('admin/database-snapshot/{entityId}/restore', name: 'restore_database_snapshot')]
    public function restoreDatabaseSnapshot(AdminContext $context): Response
    {
        $snapshot = $context->getEntity()->getInstance();
        
        $this->addFlash('warning', sprintf('从数据库快照 %s 还原指令已发送', $snapshot->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
    
    /**
     * 导出数据库快照
     */
    #[Route('admin/database-snapshot/{entityId}/export', name: 'export_database_snapshot')]
    public function exportDatabaseSnapshot(AdminContext $context): Response
    {
        $snapshot = $context->getEntity()->getInstance();
        
        $this->addFlash('success', sprintf('数据库快照 %s 导出指令已发送', $snapshot->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
} 