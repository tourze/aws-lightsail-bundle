<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Controller\Admin;

use AwsLightsailBundle\Entity\AwsCredential;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

/**
 * AWS 凭证管理控制器
 *
 * @extends AbstractCrudController<AwsCredential>
 */
#[AdminCrud(
    routePath: '/aws-lightsail/aws-credential',
    routeName: 'aws_lightsail_aws_credential'
)]
final class AwsCredentialCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AwsCredential::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('AWS 凭证')
            ->setEntityLabelInPlural('AWS 凭证列表')
            ->setPageTitle('index', 'AWS 凭证管理')
            ->setPageTitle('new', '创建 AWS 凭证')
            ->setPageTitle('edit', fn (AwsCredential $credential) => \sprintf('编辑凭证: %s', $credential->getName()))
            ->setPageTitle('detail', fn (AwsCredential $credential) => \sprintf('凭证详情: %s', $credential->getName()))
            ->setSearchFields(['name', 'accessKeyId'])
            ->setDefaultSort(['name' => 'ASC'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->hideOnForm()
            ->setMaxLength(9999)
        ;

        yield TextField::new('name', '凭证名称')
            ->setHelp('设置一个易于识别的名称')
        ;

        yield TextField::new('accessKeyId', 'Access Key ID')
            ->setHelp('AWS访问密钥ID')
        ;

        yield TextField::new('secretAccessKey', 'Secret Access Key')
            ->setHelp('AWS访问密钥')
            ->hideOnIndex()
        ;

        yield BooleanField::new('isDefault', '默认凭证')
            ->setHelp('设置为默认凭证后将优先使用')
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->set(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::DETAIL, function (Action $action) {
                return $action->setIcon('fa fa-eye')->setLabel('查看');
            })
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('name', '凭证名称'))
            ->add(BooleanFilter::new('isDefault', '默认凭证'))
        ;
    }
}
