<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Controller\Admin;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\ContainerService;
use AwsLightsailBundle\Enum\ContainerServicePowerEnum;
use AwsLightsailBundle\Enum\ContainerServiceStateEnum;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
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
 * Lightsail 容器服务管理控制器
 *
 * @extends AbstractCrudController<ContainerService>
 */
#[AdminCrud(
    routePath: '/aws-lightsail/container-service',
    routeName: 'aws_lightsail_container_service'
)]
final class ContainerServiceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ContainerService::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('容器服务')
            ->setEntityLabelInPlural('容器服务列表')
            ->setPageTitle('index', 'Lightsail 容器服务管理')
            ->setPageTitle('new', '创建容器服务')
            ->setPageTitle('edit', fn (ContainerService $service) => \sprintf('编辑容器服务: %s', $service->getName()))
            ->setPageTitle('detail', fn (ContainerService $service) => \sprintf('容器服务详情: %s', $service->getName()))
            ->setSearchFields(['name', 'url', 'region'])
            ->setDefaultSort(['name' => 'ASC'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield from $this->getBasicFields($pageName);
        yield from $this->getDeploymentFields();
        yield from $this->getDomainFields();
        yield from $this->getMetadataFields($pageName);
        yield from $this->getTimeFields();
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getBasicFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->hideOnForm()
            ->setMaxLength(9999)
        ;

        yield TextField::new('name', '服务名称');

        yield TextField::new('arn', 'AWS ARN')
            ->hideOnForm()
            ->hideOnIndex()
        ;

        yield ChoiceField::new('power', '计算能力')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => ContainerServicePowerEnum::class,
            ])
            ->formatValue(function ($value) {
                return $value instanceof ContainerServicePowerEnum ? $value->getLabel() : '';
            })
        ;

        yield IntegerField::new('scale', '规模')
            ->setHelp('服务的节点数')
        ;

        yield ChoiceField::new('state', '状态')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => ContainerServiceStateEnum::class,
            ])
            ->formatValue(function ($value) {
                return $value instanceof ContainerServiceStateEnum ? $value->getLabel() : '';
            })
        ;

        yield TextField::new('region', '区域');

        yield TextField::new('url', '服务 URL')
            ->hideOnForm()
        ;
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getDeploymentFields(): iterable
    {
        yield CodeEditorField::new('currentDeployment', '当前部署')
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return $value ? \json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '{}';
            })
        ;

        yield CodeEditorField::new('nextDeployment', '下一个部署')
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return $value ? \json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '{}';
            })
        ;

        yield CodeEditorField::new('containerImages', '容器镜像')
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return $value ? \json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '[]';
            })
        ;
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getDomainFields(): iterable
    {
        yield BooleanField::new('isPublicDomainEnabled', '公共域名已启用')
            ->renderAsSwitch(true)
        ;

        yield BooleanField::new('isPrivateDomainEnabled', '私有域名已启用')
            ->renderAsSwitch(true)
        ;

        yield CodeEditorField::new('privateDomainName', '私有域名')
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return $value ? \json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '{}';
            })
        ;

        yield TextField::new('publicDomainNames', '公共域名')
            ->hideOnIndex()
        ;
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getMetadataFields(string $pageName): iterable
    {
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
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getTimeFields(): iterable
    {
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
        $syncAction = Action::new('syncContainerService', '同步')
            ->linkToRoute('sync_container_service', function (ContainerService $service) {
                return ['entityId' => $service->getId()];
            })
            ->setIcon('fa fa-refresh')
        ;

        $deployAction = Action::new('deployContainerService', '部署')
            ->linkToRoute('deploy_container_service', function (ContainerService $service) {
                return ['entityId' => $service->getId()];
            })
            ->setIcon('fa fa-cloud-upload')
            ->setCssClass('text-primary')
        ;

        $restartAction = Action::new('restartContainerService', '重启')
            ->linkToRoute('restart_container_service', function (ContainerService $service) {
                return ['entityId' => $service->getId()];
            })
            ->setIcon('fa fa-power-off')
            ->setCssClass('text-warning')
        ;

        $registerImageAction = Action::new('registerImage', '注册镜像')
            ->linkToRoute('register_container_image', function (ContainerService $service) {
                return ['entityId' => $service->getId()];
            })
            ->setIcon('fa fa-docker')
            ->setCssClass('text-success')
        ;

        return $actions
            ->set(Crud::PAGE_INDEX, Action::DELETE)
            ->set(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $syncAction)
            ->add(Crud::PAGE_INDEX, $deployAction)
            ->add(Crud::PAGE_INDEX, $restartAction)
            ->add(Crud::PAGE_INDEX, $registerImageAction)
            ->add(Crud::PAGE_DETAIL, $syncAction)
            ->add(Crud::PAGE_DETAIL, $deployAction)
            ->add(Crud::PAGE_DETAIL, $restartAction)
            ->add(Crud::PAGE_DETAIL, $registerImageAction)
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
        $powerChoices = [];
        foreach (ContainerServicePowerEnum::cases() as $case) {
            $powerChoices[$case->getLabel()] = $case->value;
        }

        $stateChoices = [];
        foreach (ContainerServiceStateEnum::cases() as $case) {
            $stateChoices[$case->getLabel()] = $case->value;
        }

        return $filters
            ->add(TextFilter::new('name', '服务名称'))
            ->add(TextFilter::new('url', '服务 URL'))
            ->add(TextFilter::new('region', '区域'))
            ->add(ChoiceFilter::new('power', '计算能力')->setChoices($powerChoices))
            ->add(ChoiceFilter::new('state', '状态')->setChoices($stateChoices))
            ->add(BooleanFilter::new('isPublicDomainEnabled', '公共域名已启用'))
            ->add(BooleanFilter::new('isPrivateDomainEnabled', '私有域名已启用'))
            ->add(EntityFilter::new('credential', 'AWS 凭证'))
        ;
    }
}
