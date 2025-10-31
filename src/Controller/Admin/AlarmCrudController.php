<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Controller\Admin;

use AwsLightsailBundle\Entity\Alarm;
use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Enum\AlarmMetricEnum;
use AwsLightsailBundle\Enum\AlarmStateEnum;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminAction;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Lightsail 告警管理控制器
 *
 * @extends AbstractCrudController<Alarm>
 */
#[AdminCrud(routePath: '/aws-lightsail/alarm', routeName: 'aws_lightsail_alarm')]
final class AlarmCrudController extends AbstractCrudController
{
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
            ->setPageTitle('edit', fn (Alarm $alarm) => \sprintf('编辑告警: %s', $alarm->getName()))
            ->setPageTitle('detail', fn (Alarm $alarm) => \sprintf('告警详情: %s', $alarm->getName()))
            ->setSearchFields(['name', 'resourceName', 'resourceType', 'region'])
            ->setDefaultSort(['name' => 'ASC'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->hideOnForm()
            ->setMaxLength(9999)
        ;

        yield TextField::new('name', '告警名称');

        yield TextField::new('arn', 'AWS ARN')
            ->hideOnForm()
            ->hideOnIndex()
        ;

        yield TextField::new('resourceName', '资源名称')
            ->setHelp('被监控的资源名称')
        ;

        yield TextField::new('resourceType', '资源类型');

        yield ChoiceField::new('metricName', '监控指标')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => AlarmMetricEnum::class,
            ])
            ->formatValue(function ($value) {
                return $value instanceof AlarmMetricEnum ? $value->getLabel() : '';
            })
        ;

        yield ChoiceField::new('state', '状态')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => AlarmStateEnum::class,
            ])
            ->formatValue(function ($value) {
                return $value instanceof AlarmStateEnum ? $value->getLabel() : '';
            })
        ;

        yield TextField::new('region', '区域');

        yield TextField::new('comparisonOperator', '比较运算符')
            ->hideOnIndex()
        ;

        yield TextField::new('evaluationPeriods', '评估周期')
            ->hideOnIndex()
        ;

        yield NumberField::new('threshold', '阈值');

        yield TextField::new('treatMissingData', '缺失数据处理')
            ->hideOnIndex()
        ;

        yield CodeEditorField::new('contactProtocols', '联系方式协议')
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return $value ? \json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '[]';
            })
        ;

        yield CodeEditorField::new('monitoredResourceInfo', '监控资源信息')
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return $value ? \json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '{}';
            })
        ;

        yield CodeEditorField::new('datapointsToAlarm', '告警数据点')
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return $value ? \json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '{}';
            })
        ;

        yield BooleanField::new('notificationEnabled', '通知已启用')
            ->renderAsSwitch(true)
        ;

        yield DateTimeField::new('notificationTriggeredTime', '通知触发时间')
            ->hideOnForm()
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
        $syncAction = Action::new('syncAlarm', '同步')
            ->linkToCrudAction('syncAlarm')
            ->setIcon('fa fa-refresh')
            ->displayIf(function ($entity) {
                return $entity instanceof Alarm && null !== $entity->getId();
            })
        ;

        $testAction = Action::new('testAlarm', '测试告警')
            ->linkToCrudAction('testAlarm')
            ->setIcon('fa fa-bell')
            ->setCssClass('text-warning')
            ->displayIf(function ($entity) {
                return $entity instanceof Alarm && null !== $entity->getId();
            })
        ;

        $toggleNotificationAction = Action::new('toggleNotification', '启用/禁用通知')
            ->linkToCrudAction('toggleNotification')
            ->setIcon('fa fa-envelope')
            ->displayIf(function ($entity) {
                return $entity instanceof Alarm && null !== $entity->getId();
            })
        ;

        return $actions
            ->add(Crud::PAGE_DETAIL, $syncAction)
            ->add(Crud::PAGE_DETAIL, $testAction)
            ->add(Crud::PAGE_DETAIL, $toggleNotificationAction)
            ->disable(Action::NEW, Action::EDIT, Action::DELETE)
        ;
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
            ->add(EntityFilter::new('credential', 'AWS 凭证'))
        ;
    }

    /**
     * 同步告警action
     */
    #[AdminAction(routePath: '{id}/sync', routeName: 'syncAlarmAction')]
    public function syncAlarm(): Response
    {
        $context = $this->getContext();
        if (null === $context) {
            throw $this->createNotFoundException('Context not found');
        }

        $entity   = $context->getEntity();
        $instance = $entity->getInstance();
        if (null === $instance) {
            throw $this->createNotFoundException('Entity instance not found');
        }

        $entityId = $instance->getId();

        return $this->redirectToRoute('sync_alarm', ['entityId' => $entityId]);
    }

    /**
     * 测试告警action
     */
    #[AdminAction(routePath: '{id}/test', routeName: 'testAlarmAction')]
    public function testAlarm(): Response
    {
        $context = $this->getContext();
        if (null === $context) {
            throw $this->createNotFoundException('Context not found');
        }

        $entity   = $context->getEntity();
        $instance = $entity->getInstance();
        if (null === $instance) {
            throw $this->createNotFoundException('Entity instance not found');
        }

        $entityId = $instance->getId();

        return $this->redirectToRoute('test_alarm', ['entityId' => $entityId]);
    }

    /**
     * 启用/禁用通知action
     */
    #[AdminAction(routePath: '{id}/toggle-notification', routeName: 'toggleNotificationAction')]
    public function toggleNotification(): Response
    {
        $context = $this->getContext();
        if (null === $context) {
            throw $this->createNotFoundException('Context not found');
        }

        $entity   = $context->getEntity();
        $instance = $entity->getInstance();
        if (null === $instance) {
            throw $this->createNotFoundException('Entity instance not found');
        }

        $entityId = $instance->getId();

        return $this->redirectToRoute('toggle_alarm_notification', ['entityId' => $entityId]);
    }
}
