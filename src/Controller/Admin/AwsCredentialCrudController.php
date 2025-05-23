<?php

namespace AwsLightsailBundle\Controller\Admin;

use AwsLightsailBundle\Entity\AwsCredential;
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
 */
class AwsCredentialCrudController extends AbstractCrudController
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
            ->setPageTitle('edit', fn (AwsCredential $credential) => sprintf('编辑凭证: %s', $credential->getName()))
            ->setPageTitle('detail', fn (AwsCredential $credential) => sprintf('凭证详情: %s', $credential->getName()))
            ->setSearchFields(['name', 'accessKeyId', 'region'])
            ->setDefaultSort(['name' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->hideOnForm()
            ->setMaxLength(9999);
            
        yield TextField::new('name', '凭证名称')
            ->setHelp('设置一个易于识别的名称');
            
        yield TextField::new('accessKeyId', 'Access Key ID')
            ->setHelp('AWS访问密钥ID');
            
        yield TextField::new('secretAccessKey', 'Secret Access Key')
            ->setHelp('AWS访问密钥')
            ->hideOnIndex();
            
        yield TextField::new('region', '区域')
            ->setHelp('AWS区域代码，如: us-east-1');
            
        yield BooleanField::new('isDefault', '默认凭证')
            ->setHelp('设置为默认凭证后将优先使用');
            
        yield DateTimeField::new('createdAt', '创建时间')
            ->hideOnForm();
            
        yield DateTimeField::new('updatedAt', '更新时间')
            ->hideOnForm();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->setIcon('fa fa-trash')->setLabel('删除');
            })
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action->setIcon('fa fa-edit')->setLabel('编辑');
            })
            ->update(Crud::PAGE_INDEX, Action::DETAIL, function (Action $action) {
                return $action->setIcon('fa fa-eye')->setLabel('查看');
            })
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN, function (Action $action) {
                return $action->setLabel('保存');
            });
    }
    
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('name', '凭证名称'))
            ->add(TextFilter::new('region', '区域'))
            ->add(BooleanFilter::new('isDefault', '默认凭证'));
    }
}
