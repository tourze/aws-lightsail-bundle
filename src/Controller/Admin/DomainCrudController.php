<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Controller\Admin;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Domain;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

/**
 * Lightsail 域名管理控制器
 *
 * @extends AbstractCrudController<Domain>
 */
#[AdminCrud(
    routePath: '/aws-lightsail/domain',
    routeName: 'aws_lightsail_domain'
)]
final class DomainCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Domain::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('域名')
            ->setEntityLabelInPlural('域名列表')
            ->setPageTitle('index', 'Lightsail 域名管理')
            ->setPageTitle('new', '创建域名')
            ->setPageTitle('edit', fn (Domain $domain) => \sprintf('编辑域名: %s', $domain->getName()))
            ->setPageTitle('detail', fn (Domain $domain) => \sprintf('域名详情: %s', $domain->getName()))
            ->setSearchFields(['name', 'region'])
            ->setDefaultSort(['name' => 'ASC'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->hideOnForm()
            ->setMaxLength(9999)
        ;

        yield TextField::new('name', '域名');

        yield TextField::new('arn', 'AWS ARN')
            ->hideOnForm()
            ->hideOnIndex()
        ;

        yield TextField::new('region', '区域');

        yield BooleanField::new('isManaged', '是否托管')
            ->renderAsSwitch(true)
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
        $syncAction = Action::new('sync', '同步')
            ->linkToUrl(function ($entity) {
                return '#'; // 暂时设置为空链接，避免404错误
            })
            ->setIcon('fa fa-refresh')
        ;

        return $actions
            ->set(Crud::PAGE_INDEX, Action::DELETE)
            ->set(Crud::PAGE_INDEX, Action::DETAIL)
            ->set(Crud::PAGE_INDEX, Action::EDIT)
            ->add(Crud::PAGE_INDEX, $syncAction)
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
        return $filters
            ->add(TextFilter::new('name', '域名'))
            ->add(TextFilter::new('region', '区域'))
            ->add(BooleanFilter::new('isManaged', '是否托管'))
            ->add(EntityFilter::new('credential', 'AWS 凭证'))
        ;
    }
}
