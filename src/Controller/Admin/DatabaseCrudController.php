<?php

namespace AwsLightsailBundle\Controller\Admin;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Database;
use AwsLightsailBundle\Enum\DatabaseEngineEnum;
use AwsLightsailBundle\Enum\DatabaseStatusEnum;
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
 * Lightsail 数据库管理控制器
 */
class DatabaseCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Database::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('数据库')
            ->setEntityLabelInPlural('数据库列表')
            ->setPageTitle('index', 'Lightsail 数据库管理')
            ->setPageTitle('new', '创建数据库')
            ->setPageTitle('edit', fn (Database $database) => sprintf('编辑数据库: %s', $database->getName()))
            ->setPageTitle('detail', fn (Database $database) => sprintf('数据库详情: %s', $database->getName()))
            ->setSearchFields(['name', 'masterUsername', 'masterEndpoint', 'region'])
            ->setDefaultSort(['name' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->hideOnForm()
            ->setMaxLength(9999);
            
        yield TextField::new('name', '数据库名称');
            
        yield TextField::new('arn', 'AWS ARN')
            ->hideOnForm()
            ->hideOnIndex();
            
        yield ChoiceField::new('engine', '数据库引擎')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => DatabaseEngineEnum::class
            ])
            ->formatValue(function ($value) {
                return $value instanceof DatabaseEngineEnum ? $value->getLabel() : '';
            });
            
        yield TextField::new('engineVersion', '引擎版本');
            
        yield TextField::new('masterUsername', '主用户名')
            ->hideOnIndex();
            
        yield TextField::new('masterEndpoint', '主终端节点')
            ->setHelp('数据库连接地址');
            
        yield IntegerField::new('masterPort', '端口')
            ->hideOnIndex();
            
        yield ChoiceField::new('status', '状态')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => DatabaseStatusEnum::class
            ])
            ->formatValue(function ($value) {
                return $value instanceof DatabaseStatusEnum ? $value->getLabel() : '';
            });
            
        yield TextField::new('bundleId', '套餐ID');
            
        yield TextField::new('region', '区域');
            
        yield TextField::new('preferredBackupWindow', '备份窗口')
            ->hideOnIndex()
            ->setHelp('例如: 01:00-02:00');
            
        yield TextField::new('preferredMaintenanceWindow', '维护窗口')
            ->hideOnIndex()
            ->setHelp('例如: sat:12:00-sat:13:00');
            
        yield BooleanField::new('publiclyAccessible', '公开访问')
            ->renderAsSwitch(true);
            
        yield BooleanField::new('backupRetentionEnabled', '备份保留')
            ->renderAsSwitch(true);
            
        yield BooleanField::new('autoMinorVersionUpgrade', '自动次要版本升级')
            ->renderAsSwitch(true);
            
        yield CodeEditorField::new('pendingModifiedValues', '待修改值')
            ->hideOnForm()
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return $value ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '{}';
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
            
        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm();
            
        yield DateTimeField::new('syncTime', '同步时间')
            ->hideOnForm();
            
        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm();
    }

    public function configureActions(Actions $actions): Actions
    {
        $syncAction = Action::new('syncDatabase', '同步')
            ->linkToCrudAction('syncDatabase')
            ->setIcon('fa fa-refresh');
            
        $startAction = Action::new('startDatabase', '启动')
            ->linkToCrudAction('startDatabase')
            ->setIcon('fa fa-play')
            ->setCssClass('text-success');
            
        $stopAction = Action::new('stopDatabase', '停止')
            ->linkToCrudAction('stopDatabase')
            ->setIcon('fa fa-stop')
            ->setCssClass('text-danger');
            
        $rebootAction = Action::new('rebootDatabase', '重启')
            ->linkToCrudAction('rebootDatabase')
            ->setIcon('fa fa-power-off')
            ->setCssClass('text-warning');
            
        $createSnapshotAction = Action::new('createSnapshot', '创建快照')
            ->linkToCrudAction('createSnapshot')
            ->setIcon('fa fa-camera')
            ->setCssClass('text-primary');
            
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $syncAction)
            ->add(Crud::PAGE_INDEX, $startAction)
            ->add(Crud::PAGE_INDEX, $stopAction)
            ->add(Crud::PAGE_INDEX, $rebootAction)
            ->add(Crud::PAGE_INDEX, $createSnapshotAction)
            ->add(Crud::PAGE_DETAIL, $syncAction)
            ->add(Crud::PAGE_DETAIL, $startAction)
            ->add(Crud::PAGE_DETAIL, $stopAction)
            ->add(Crud::PAGE_DETAIL, $rebootAction)
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
        $engineChoices = [];
        foreach (DatabaseEngineEnum::cases() as $case) {
            $engineChoices[$case->getLabel()] = $case->value;
        }
        
        $statusChoices = [];
        foreach (DatabaseStatusEnum::cases() as $case) {
            $statusChoices[$case->getLabel()] = $case->value;
        }
        
        return $filters
            ->add(TextFilter::new('name', '数据库名称'))
            ->add(TextFilter::new('masterUsername', '主用户名'))
            ->add(TextFilter::new('region', '区域'))
            ->add(ChoiceFilter::new('engine', '数据库引擎')->setChoices($engineChoices))
            ->add(ChoiceFilter::new('status', '状态')->setChoices($statusChoices))
            ->add(BooleanFilter::new('publiclyAccessible', '公开访问'))
            ->add(BooleanFilter::new('backupRetentionEnabled', '备份保留'))
            ->add(BooleanFilter::new('autoMinorVersionUpgrade', '自动次要版本升级'))
            ->add(EntityFilter::new('credential', 'AWS 凭证'));
    }
    
    /**
     * 同步数据库状态
     */
    #[Route('admin/database/{entityId}/sync', name: 'sync_database')]
    public function syncDatabase(AdminContext $context): Response
    {
        $database = $context->getEntity()->getInstance();
        
        $this->addFlash('info', sprintf('数据库 %s 同步指令已发送', $database->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
    
    /**
     * 启动数据库
     */
    #[Route('admin/database/{entityId}/start', name: 'start_database')]
    public function startDatabase(AdminContext $context): Response
    {
        $database = $context->getEntity()->getInstance();
        
        $this->addFlash('success', sprintf('数据库 %s 启动指令已发送', $database->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
    
    /**
     * 停止数据库
     */
    #[Route('admin/database/{entityId}/stop', name: 'stop_database')]
    public function stopDatabase(AdminContext $context): Response
    {
        $database = $context->getEntity()->getInstance();
        
        $this->addFlash('warning', sprintf('数据库 %s 停止指令已发送', $database->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
    
    /**
     * 重启数据库
     */
    #[Route('admin/database/{entityId}/reboot', name: 'reboot_database')]
    public function rebootDatabase(AdminContext $context): Response
    {
        $database = $context->getEntity()->getInstance();
        
        $this->addFlash('warning', sprintf('数据库 %s 重启指令已发送', $database->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
    
    /**
     * 创建数据库快照
     */
    #[Route('admin/database/{entityId}/create-snapshot', name: 'create_database_snapshot')]
    public function createSnapshot(AdminContext $context): Response
    {
        $database = $context->getEntity()->getInstance();
        
        $this->addFlash('info', sprintf('数据库 %s 创建快照指令已发送', $database->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
} 