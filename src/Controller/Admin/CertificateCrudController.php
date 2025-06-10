<?php

namespace AwsLightsailBundle\Controller\Admin;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Certificate;
use AwsLightsailBundle\Enum\CertificateStatusEnum;
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
 * Lightsail 证书管理控制器
 */
class CertificateCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Certificate::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('证书')
            ->setEntityLabelInPlural('证书列表')
            ->setPageTitle('index', 'Lightsail 证书管理')
            ->setPageTitle('new', '创建证书')
            ->setPageTitle('edit', fn (Certificate $certificate) => sprintf('编辑证书: %s', $certificate->getName()))
            ->setPageTitle('detail', fn (Certificate $certificate) => sprintf('证书详情: %s', $certificate->getName()))
            ->setSearchFields(['name', 'domainName', 'serialNumber', 'region'])
            ->setDefaultSort(['name' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->hideOnForm()
            ->setMaxLength(9999);
            
        yield TextField::new('name', '证书名称');
            
        yield TextField::new('arn', 'AWS ARN')
            ->hideOnForm()
            ->hideOnIndex();
            
        yield TextField::new('domainName', '域名')
            ->setHelp('证书的主域名');
            
        yield CodeEditorField::new('subjectAlternativeNames', '备用域名')
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return $value ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '[]';
            });
            
        yield CodeEditorField::new('domainValidationRecords', '域名验证记录')
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return $value ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '[]';
            });
            
        yield ChoiceField::new('status', '状态')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => CertificateStatusEnum::class
            ])
            ->formatValue(function ($value) {
                return $value instanceof CertificateStatusEnum ? $value->getLabel() : '';
            });
            
        yield TextField::new('region', '区域');
            
        yield DateTimeField::new('notBefore', '生效时间')
            ->hideOnForm();
            
        yield DateTimeField::new('notAfter', '过期时间')
            ->hideOnForm();
            
        yield TextField::new('serialNumber', '序列号')
            ->hideOnIndex();
            
        yield CodeEditorField::new('keyAlgorithm', '密钥算法')
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return $value ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '{}';
            });
            
        yield BooleanField::new('isManaged', '由 AWS 管理')
            ->renderAsSwitch(false)
            ->setFormTypeOption('disabled', true);
            
        yield BooleanField::new('inUse', '正在使用')
            ->renderAsSwitch(false)
            ->setFormTypeOption('disabled', true);
            
        yield CodeEditorField::new('supportedOnResources', '支持的资源')
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return $value ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '[]';
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
        $syncAction = Action::new('syncCertificate', '同步')
            ->linkToCrudAction('syncCertificate')
            ->setIcon('fa fa-refresh');
            
        $validateAction = Action::new('validateCertificate', '验证证书')
            ->linkToCrudAction('validateCertificate')
            ->setIcon('fa fa-check')
            ->setCssClass('text-success');
            
        $exportAction = Action::new('exportCertificate', '导出证书')
            ->linkToCrudAction('exportCertificate')
            ->setIcon('fa fa-download')
            ->setCssClass('text-primary');
            
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $syncAction)
            ->add(Crud::PAGE_INDEX, $validateAction)
            ->add(Crud::PAGE_INDEX, $exportAction)
            ->add(Crud::PAGE_DETAIL, $syncAction)
            ->add(Crud::PAGE_DETAIL, $validateAction)
            ->add(Crud::PAGE_DETAIL, $exportAction)
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
        $statusChoices = [];
        foreach (CertificateStatusEnum::cases() as $case) {
            $statusChoices[$case->getLabel()] = $case->value;
        }
        
        return $filters
            ->add(TextFilter::new('name', '证书名称'))
            ->add(TextFilter::new('domainName', '域名'))
            ->add(TextFilter::new('region', '区域'))
            ->add(ChoiceFilter::new('status', '状态')->setChoices($statusChoices))
            ->add(BooleanFilter::new('isManaged', '由 AWS 管理'))
            ->add(BooleanFilter::new('inUse', '正在使用'))
            ->add(EntityFilter::new('credential', 'AWS 凭证'));
    }
    
    /**
     * 同步证书状态
     */
    #[Route('admin/certificate/{entityId}/sync', name: 'sync_certificate')]
    public function syncCertificate(AdminContext $context): Response
    {
        $certificate = $context->getEntity()->getInstance();
        
        $this->addFlash('info', sprintf('证书 %s 同步指令已发送', $certificate->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
    
    /**
     * 验证证书
     */
    #[Route('admin/certificate/{entityId}/validate', name: 'validate_certificate')]
    public function validateCertificate(AdminContext $context): Response
    {
        $certificate = $context->getEntity()->getInstance();
        
        $this->addFlash('warning', sprintf('证书 %s 验证指令已发送', $certificate->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
    
    /**
     * 导出证书
     */
    #[Route('admin/certificate/{entityId}/export', name: 'export_certificate')]
    public function exportCertificate(AdminContext $context): Response
    {
        $certificate = $context->getEntity()->getInstance();
        
        $this->addFlash('info', sprintf('证书 %s 导出指令已发送', $certificate->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
} 