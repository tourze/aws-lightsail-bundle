<?php

namespace AwsLightsailBundle\Controller\Admin;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Instance;
use AwsLightsailBundle\Enum\InstanceBlueprintEnum;
use AwsLightsailBundle\Enum\InstanceBundleEnum;
use AwsLightsailBundle\Enum\InstanceStateEnum;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Lightsail 实例管理控制器
 */
class InstanceCrudController extends AbstractCrudController
{
    public function __construct(private readonly AdminUrlGenerator $adminUrlGenerator)
    {
    }

    public static function getEntityFqcn(): string
    {
        return Instance::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Lightsail 实例')
            ->setEntityLabelInPlural('Lightsail 实例列表')
            ->setPageTitle('index', 'Lightsail 实例管理')
            ->setPageTitle('new', '创建 Lightsail 实例')
            ->setPageTitle('edit', fn(Instance $instance) => sprintf('编辑实例: %s', $instance->getName()))
            ->setPageTitle('detail', fn(Instance $instance) => sprintf('实例详情: %s', $instance->getName()))
            ->setSearchFields(['name', 'publicIpAddress', 'privateIpAddress', 'region'])
            ->setDefaultSort(['name' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->hideOnForm()
            ->setMaxLength(9999);

        yield TextField::new('name', '实例名称');

        yield TextField::new('arn', 'AWS ARN')
            ->hideOnForm()
            ->hideOnIndex();

        yield ChoiceField::new('state', '状态')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => InstanceStateEnum::class,
                'disabled' => true
            ])
            ->formatValue(function ($value) {
                return $value instanceof InstanceStateEnum ? $value->getLabel() : '';
            });

        yield ChoiceField::new('blueprint', '蓝图')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => InstanceBlueprintEnum::class,
                'disabled' => true
            ])
            ->formatValue(function ($value) {
                return $value instanceof InstanceBlueprintEnum ? $value->getLabel() : '';
            });

        yield ChoiceField::new('bundle', '套餐')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => InstanceBundleEnum::class,
                'disabled' => true
            ])
            ->formatValue(function ($value) {
                return $value instanceof InstanceBundleEnum ? $value->getLabel() : '';
            });

        yield TextField::new('region', '区域');

        yield TextField::new('publicIpAddress', '公网IP')
            ->hideOnForm();

        yield TextField::new('privateIpAddress', '私网IP')
            ->hideOnForm();

        yield TextField::new('keyPairName', '密钥对')
            ->hideOnForm();

        yield TextField::new('username', '用户名')
            ->hideOnForm();

        yield BooleanField::new('isMonitoring', '监控状态')
            ->hideOnForm();

        yield TextField::new('supportCode', '支持代码')
            ->hideOnForm()
            ->hideOnIndex();

        yield CodeEditorField::new('hardware', '硬件配置')
            ->hideOnForm()
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            });

        yield CodeEditorField::new('networking', '网络配置')
            ->hideOnForm()
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            });

        yield CodeEditorField::new('tags', '标签')
            ->hideOnForm()
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
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
        $startAction = Action::new('startInstance', '启动')
            ->linkToCrudAction('startInstance')
            ->setIcon('fa fa-play')
            ->displayIf(static function (Instance $instance) {
                return $instance->getState() === InstanceStateEnum::STOPPED;
            });

        $stopAction = Action::new('stopInstance', '停止')
            ->linkToCrudAction('stopInstance')
            ->setIcon('fa fa-stop')
            ->displayIf(static function (Instance $instance) {
                return $instance->getState() === InstanceStateEnum::RUNNING;
            });

        $rebootAction = Action::new('rebootInstance', '重启')
            ->linkToCrudAction('rebootInstance')
            ->setIcon('fa fa-sync')
            ->displayIf(static function (Instance $instance) {
                return $instance->getState() === InstanceStateEnum::RUNNING;
            });

        $syncAction = Action::new('syncInstance', '同步')
            ->linkToCrudAction('syncInstance')
            ->setIcon('fa fa-refresh');

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $startAction)
            ->add(Crud::PAGE_INDEX, $stopAction)
            ->add(Crud::PAGE_INDEX, $rebootAction)
            ->add(Crud::PAGE_INDEX, $syncAction)
            ->add(Crud::PAGE_DETAIL, $startAction)
            ->add(Crud::PAGE_DETAIL, $stopAction)
            ->add(Crud::PAGE_DETAIL, $rebootAction)
            ->add(Crud::PAGE_DETAIL, $syncAction)
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
        foreach (InstanceStateEnum::cases() as $case) {
            $stateChoices[$case->getLabel()] = $case->value;
        }

        $blueprintChoices = [];
        foreach (InstanceBlueprintEnum::cases() as $case) {
            $blueprintChoices[$case->getLabel()] = $case->value;
        }

        $bundleChoices = [];
        foreach (InstanceBundleEnum::cases() as $case) {
            $bundleChoices[$case->getLabel()] = $case->value;
        }

        return $filters
            ->add(TextFilter::new('name', '实例名称'))
            ->add(ChoiceFilter::new('state', '状态')->setChoices($stateChoices))
            ->add(ChoiceFilter::new('blueprint', '蓝图')->setChoices($blueprintChoices))
            ->add(ChoiceFilter::new('bundle', '套餐')->setChoices($bundleChoices))
            ->add(TextFilter::new('region', '区域'))
            ->add(TextFilter::new('publicIpAddress', '公网IP'))
            ->add(EntityFilter::new('credential', 'AWS 凭证'));
    }

    /**
     * 启动实例
     */
    #[Route('admin/instance/{entityId}/start', name: 'start_instance')]
    public function startInstance(AdminContext $context): Response
    {
        $instance = $context->getEntity()->getInstance();

        $this->addFlash('info', sprintf('实例 %s 启动指令已发送', $instance->getName()));

        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }

    /**
     * 停止实例
     */
    #[Route('admin/instance/{entityId}/stop', name: 'stop_instance')]
    public function stopInstance(AdminContext $context): Response
    {
        $instance = $context->getEntity()->getInstance();

        $this->addFlash('info', sprintf('实例 %s 停止指令已发送', $instance->getName()));

        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }

    /**
     * 重启实例
     */
    #[Route('admin/instance/{entityId}/reboot', name: 'reboot_instance')]
    public function rebootInstance(AdminContext $context): Response
    {
        $instance = $context->getEntity()->getInstance();

        $this->addFlash('info', sprintf('实例 %s 重启指令已发送', $instance->getName()));

        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }

    /**
     * 同步实例状态
     */
    #[Route('admin/instance/{entityId}/sync', name: 'sync_instance')]
    public function syncInstance(AdminContext $context): Response
    {
        $instance = $context->getEntity()->getInstance();

        $this->addFlash('info', sprintf('实例 %s 同步指令已发送', $instance->getName()));

        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
}
