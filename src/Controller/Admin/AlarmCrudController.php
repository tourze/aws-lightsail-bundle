<?php

namespace AwsLightsailBundle\Controller\Admin;

use AwsLightsailBundle\Entity\Alarm;
use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Enum\AlarmMetricEnum;
use AwsLightsailBundle\Enum\AlarmStateEnum;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
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
 * Lightsail 告警管理控制器
 */
class AlarmCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Alarm::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('告警')
            ->setEntityLabelInPlural('告警列表')
            ->setPageTitle('index', 'Lightsail 告警管理')
            ->setPageTitle('new', '创建告警')
            ->setPageTitle('edit', fn (Alarm $alarm) => sprintf('编辑告警: %s', $alarm->getName()))
            ->setPageTitle('detail', fn (Alarm $alarm) => sprintf('告警详情: %s', $alarm->getName()))
            ->setSearchFields(['name', 'resourceName', 'resourceType', 'region'])
            ->setDefaultSort(['name' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->hideOnForm()
            ->setMaxLength(9999);
            
        yield TextField::new('name', '告警名称');
            
        yield TextField::new('arn', 'AWS ARN')
            ->hideOnForm()
            ->hideOnIndex();
            
        yield TextField::new('resourceName', '资源名称')
            ->setHelp('被监控的资源名称');
            
        yield TextField::new('resourceType', '资源类型');
            
        yield ChoiceField::new('metricName', '监控指标')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => AlarmMetricEnum::class
            ])
            ->formatValue(function ($value) {
                return $value instanceof AlarmMetricEnum ? $value->getLabel() : '';
            });
            
        yield ChoiceField::new('state', '状态')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => AlarmStateEnum::class
            ])
            ->formatValue(function ($value) {
                return $value instanceof AlarmStateEnum ? $value->getLabel() : '';
            });
            
        yield TextField::new('region', '区域');
            
        yield TextField::new('comparisonOperator', '比较运算符')
            ->hideOnIndex();
            
        yield TextField::new('evaluationPeriods', '评估周期')
            ->hideOnIndex();
            
        yield NumberField::new('threshold', '阈值');
            
        yield TextField::new('treatMissingData', '缺失数据处理')
            ->hideOnIndex();
            
        yield CodeEditorField::new('contactProtocols', '联系方式协议')
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return $value ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '[]';
            });
            
        yield CodeEditorField::new('monitoredResourceInfo', '监控资源信息')
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return $value ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '{}';
            });
            
        yield CodeEditorField::new('datapointsToAlarm', '告警数据点')
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return $value ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '{}';
            });
            
        yield BooleanField::new('notificationEnabled', '通知已启用')
            ->renderAsSwitch(true);
            
        yield DateTimeField::new('notificationTriggeredTime', '通知触发时间')
            ->hideOnForm();
            
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
        $syncAction = Action::new('syncAlarm', '同步')
            ->linkToCrudAction('syncAlarm')
            ->setIcon('fa fa-refresh');
            
        $testAction = Action::new('testAlarm', '测试告警')
            ->linkToCrudAction('testAlarm')
            ->setIcon('fa fa-bell')
            ->setCssClass('text-warning');
            
        $toggleNotificationAction = Action::new('toggleNotification', '启用/禁用通知')
            ->linkToCrudAction('toggleNotification')
            ->setIcon('fa fa-envelope');
            
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $syncAction)
            ->add(Crud::PAGE_INDEX, $testAction)
            ->add(Crud::PAGE_INDEX, $toggleNotificationAction)
            ->add(Crud::PAGE_DETAIL, $syncAction)
            ->add(Crud::PAGE_DETAIL, $testAction)
            ->add(Crud::PAGE_DETAIL, $toggleNotificationAction)
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
        $metricChoices = [];
        foreach (AlarmMetricEnum::cases() as $case) {
            $metricChoices[$case->getLabel()] = $case->value;
        }
        
        $stateChoices = [];
        foreach (AlarmStateEnum::cases() as $case) {
            $stateChoices[$case->getLabel()] = $case->value;
        }
        
        return $filters
            ->add(TextFilter::new('name', '告警名称'))
            ->add(TextFilter::new('resourceName', '资源名称'))
            ->add(TextFilter::new('resourceType', '资源类型'))
            ->add(TextFilter::new('region', '区域'))
            ->add(ChoiceFilter::new('metricName', '监控指标')->setChoices($metricChoices))
            ->add(ChoiceFilter::new('state', '状态')->setChoices($stateChoices))
            ->add(BooleanFilter::new('notificationEnabled', '通知已启用'))
            ->add(EntityFilter::new('credential', 'AWS 凭证'));
    }
    
    /**
     * 同步告警状态
     */
    #[Route('admin/alarm/{entityId}/sync', name: 'sync_alarm')]
    public function syncAlarm(AdminContext $context): Response
    {
        $alarm = $context->getEntity()->getInstance();
        
        $this->addFlash('info', sprintf('告警 %s 同步指令已发送', $alarm->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
    
    /**
     * 测试告警
     */
    #[Route('admin/alarm/{entityId}/test', name: 'test_alarm')]
    public function testAlarm(AdminContext $context): Response
    {
        $alarm = $context->getEntity()->getInstance();
        
        $this->addFlash('warning', sprintf('告警 %s 测试指令已发送', $alarm->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
    
    /**
     * 启用/禁用通知
     */
    #[Route('admin/alarm/{entityId}/toggle-notification', name: 'toggle_alarm_notification')]
    public function toggleNotification(AdminContext $context): Response
    {
        $alarm = $context->getEntity()->getInstance();
        $currentState = $alarm->isNotificationEnabled();
        
        $alarm->setNotificationEnabled(!$currentState);
        $this->entityManager->flush();
        
        $newState = $alarm->isNotificationEnabled() ? '启用' : '禁用';
        $this->addFlash('success', sprintf('告警 %s 通知已%s', $alarm->getName(), $newState));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
} 