<?php

namespace AwsLightsailBundle\Controller\Admin;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\ContactMethod;
use AwsLightsailBundle\Enum\ContactMethodStatusEnum;
use AwsLightsailBundle\Enum\ContactMethodTypeEnum;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

/**
 * Lightsail 联系方式管理控制器
 */
class ContactMethodCrudController extends AbstractCrudController
{

    public static function getEntityFqcn(): string
    {
        return ContactMethod::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('联系方式')
            ->setEntityLabelInPlural('联系方式列表')
            ->setPageTitle('index', 'Lightsail 联系方式管理')
            ->setPageTitle('new', '创建联系方式')
            ->setPageTitle('edit', fn (ContactMethod $method) => sprintf('编辑联系方式: %s', $method->getName()))
            ->setPageTitle('detail', fn (ContactMethod $method) => sprintf('联系方式详情: %s', $method->getName()))
            ->setSearchFields(['name', 'contactEndpoint', 'protocol', 'region'])
            ->setDefaultSort(['name' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->hideOnForm()
            ->setMaxLength(9999);
            
        yield TextField::new('name', '联系方式名称');
            
        yield TextField::new('arn', 'AWS ARN')
            ->hideOnForm()
            ->hideOnIndex();
            
        yield ChoiceField::new('type', '类型')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => ContactMethodTypeEnum::class
            ])
            ->formatValue(function ($value) {
                return $value instanceof ContactMethodTypeEnum ? $value->getLabel() : '';
            });
            
        yield TextField::new('contactEndpoint', '联系终端')
            ->setHelp('电子邮件地址或手机号码');
            
        yield ChoiceField::new('status', '状态')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => ContactMethodStatusEnum::class
            ])
            ->formatValue(function ($value) {
                return $value instanceof ContactMethodStatusEnum ? $value->getLabel() : '';
            });
            
        yield TextField::new('region', '区域');
            
        yield TextField::new('protocol', '协议')
            ->hideOnIndex();
            
        yield DateTimeField::new('lastVerifiedTime', '上次验证时间')
            ->hideOnForm();
            
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
        $syncAction = Action::new('syncContactMethod', '同步')
            ->linkToRoute('sync_contact_method', function (ContactMethod $contactMethod) {
                return ['entityId' => $contactMethod->getId()];
            })
            ->setIcon('fa fa-refresh');
            
        $verifyAction = Action::new('verifyContactMethod', '发送验证')
            ->linkToRoute('verify_contact_method', function (ContactMethod $contactMethod) {
                return ['entityId' => $contactMethod->getId()];
            })
            ->setIcon('fa fa-check')
            ->setCssClass('text-success');
            
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $syncAction)
            ->add(Crud::PAGE_INDEX, $verifyAction)
            ->add(Crud::PAGE_DETAIL, $syncAction)
            ->add(Crud::PAGE_DETAIL, $verifyAction)
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
        $typeChoices = [];
        foreach (ContactMethodTypeEnum::cases() as $case) {
            $typeChoices[$case->getLabel()] = $case->value;
        }
        
        $statusChoices = [];
        foreach (ContactMethodStatusEnum::cases() as $case) {
            $statusChoices[$case->getLabel()] = $case->value;
        }
        
        return $filters
            ->add(TextFilter::new('name', '联系方式名称'))
            ->add(TextFilter::new('contactEndpoint', '联系终端'))
            ->add(TextFilter::new('region', '区域'))
            ->add(ChoiceFilter::new('type', '类型')->setChoices($typeChoices))
            ->add(ChoiceFilter::new('status', '状态')->setChoices($statusChoices))
            ->add(EntityFilter::new('credential', 'AWS 凭证'));
    }
} 