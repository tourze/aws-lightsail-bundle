<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Controller\Admin;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Database;
use AwsLightsailBundle\Enum\DatabaseEngineEnum;
use AwsLightsailBundle\Enum\DatabaseStatusEnum;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
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
use Symfony\Component\Form\Extension\Core\Type\EnumType;

/**
 * Lightsail 数据库管理控制器
 *
 * @extends AbstractCrudController<Database>
 */
#[AdminCrud(
    routePath: '/aws-lightsail/database',
    routeName: 'aws_lightsail_database'
)]
final class DatabaseCrudController extends AbstractCrudController
{
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
            ->setPageTitle('edit', fn (Database $database) => \sprintf('编辑数据库: %s', $database->getName()))
            ->setPageTitle('detail', fn (Database $database) => \sprintf('数据库详情: %s', $database->getName()))
            ->setSearchFields(['name', 'masterUsername', 'masterEndpoint', 'region'])
            ->setDefaultSort(['name' => 'ASC'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->hideOnForm()
            ->setMaxLength(9999)
        ;

        yield TextField::new('name', '数据库名称');

        yield TextField::new('arn', 'AWS ARN')
            ->hideOnForm()
            ->hideOnIndex()
        ;

        yield ChoiceField::new('engine', '数据库引擎')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => DatabaseEngineEnum::class,
            ])
            ->formatValue(function ($value) {
                return $value instanceof DatabaseEngineEnum ? $value->getLabel() : '';
            })
        ;

        yield TextField::new('engineVersion', '引擎版本');

        yield TextField::new('masterUsername', '主用户名')
            ->hideOnIndex()
        ;

        yield TextField::new('masterEndpoint', '主终端节点')
            ->setHelp('数据库连接地址')
        ;

        yield IntegerField::new('masterPort', '端口')
            ->hideOnIndex()
        ;

        yield ChoiceField::new('status', '状态')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => DatabaseStatusEnum::class,
            ])
            ->formatValue(function ($value) {
                return $value instanceof DatabaseStatusEnum ? $value->getLabel() : '';
            })
        ;

        yield TextField::new('bundleId', '套餐ID');

        yield TextField::new('region', '区域');

        yield TextField::new('preferredBackupWindow', '备份窗口')
            ->hideOnIndex()
            ->setHelp('例如: 01:00-02:00')
        ;

        yield TextField::new('preferredMaintenanceWindow', '维护窗口')
            ->hideOnIndex()
            ->setHelp('例如: sat:12:00-sat:13:00')
        ;

        yield BooleanField::new('publiclyAccessible', '公开访问')
            ->renderAsSwitch(true)
        ;

        yield BooleanField::new('backupRetentionEnabled', '备份保留')
            ->renderAsSwitch(true)
        ;

        yield BooleanField::new('autoMinorVersionUpgrade', '自动次要版本升级')
            ->renderAsSwitch(true)
        ;

        yield CodeEditorField::new('pendingModifiedValues', '待修改值')
            ->hideOnForm()
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return $value ? \json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '{}';
            })
        ;

        yield CodeEditorField::new('tags', '标签')
            ->hideOnForm()
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return $value ? \json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '{}';
            })
        ;

        yield AssociationField::new('credential', 'AWS 凭证')
            ->setFormTypeOption('disabled', Crud::PAGE_NEW !== $pageName)
            ->formatValue(function ($value) {
                return $value instanceof AwsCredential ? $value->getName() : '';
            })
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
        ;

        yield DateTimeField::new('syncTime', '同步时间')
            ->hideOnForm()
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $syncAction = Action::new('syncDatabase', '同步')
            ->linkToRoute('sync_database', function (Database $database) {
                return ['entityId' => $database->getId()];
            })
            ->setIcon('fa fa-refresh')
        ;

        $startAction = Action::new('startDatabase', '启动')
            ->linkToRoute('start_database', function (Database $database) {
                return ['entityId' => $database->getId()];
            })
            ->setIcon('fa fa-play')
            ->setCssClass('text-success')
        ;

        $stopAction = Action::new('stopDatabase', '停止')
            ->linkToRoute('stop_database', function (Database $database) {
                return ['entityId' => $database->getId()];
            })
            ->setIcon('fa fa-stop')
            ->setCssClass('text-danger')
        ;

        $rebootAction = Action::new('rebootDatabase', '重启')
            ->linkToRoute('reboot_database', function (Database $database) {
                return ['entityId' => $database->getId()];
            })
            ->setIcon('fa fa-power-off')
            ->setCssClass('text-warning')
        ;

        $createSnapshotAction = Action::new('createSnapshot', '创建快照')
            ->linkToRoute('create_database_snapshot', function (Database $database) {
                return ['entityId' => $database->getId()];
            })
            ->setIcon('fa fa-camera')
            ->setCssClass('text-primary')
        ;

        return $actions
            ->set(Crud::PAGE_INDEX, Action::DELETE)
            ->set(Crud::PAGE_INDEX, Action::DETAIL)
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
            })
        ;
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
            ->add(EntityFilter::new('credential', 'AWS 凭证'))
        ;
    }
}
