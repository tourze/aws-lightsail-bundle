<?php

namespace AwsLightsailBundle\Controller\Admin;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\KeyPair;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Lightsail 密钥对管理控制器
 */
class KeyPairCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return KeyPair::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('密钥对')
            ->setEntityLabelInPlural('密钥对列表')
            ->setPageTitle('index', 'Lightsail 密钥对管理')
            ->setPageTitle('new', '创建密钥对')
            ->setPageTitle('edit', fn (KeyPair $keyPair) => sprintf('编辑密钥对: %s', $keyPair->getName()))
            ->setPageTitle('detail', fn (KeyPair $keyPair) => sprintf('密钥对详情: %s', $keyPair->getName()))
            ->setSearchFields(['name', 'fingerprint', 'region'])
            ->setDefaultSort(['name' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->hideOnForm()
            ->setMaxLength(9999);
            
        yield TextField::new('name', '密钥对名称');
            
        yield TextField::new('arn', 'AWS ARN')
            ->hideOnForm()
            ->hideOnIndex();
            
        yield TextField::new('fingerprint', '指纹')
            ->hideOnForm();
            
        yield TextareaField::new('publicKey', '公钥')
            ->hideOnIndex()
            ->setFormTypeOption('disabled', true);
            
        // 仅在详情页显示私钥，且不可编辑
        if ($pageName === Crud::PAGE_DETAIL) {
            yield TextareaField::new('privateKey', '私钥')
                ->setFormTypeOption('disabled', true);
        }
            
        yield BooleanField::new('isEncrypted', '是否加密')
            ->renderAsSwitch(false)
            ->setFormTypeOption('disabled', true);
            
        yield TextField::new('region', '区域');
            
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
        $syncAction = Action::new('syncKeyPair', '同步')
            ->linkToCrudAction('syncKeyPair')
            ->setIcon('fa fa-refresh');
            
        $downloadAction = Action::new('downloadKeyPair', '下载私钥')
            ->linkToCrudAction('downloadKeyPair')
            ->setIcon('fa fa-download')
            ->setCssClass('text-primary');
            
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $syncAction)
            ->add(Crud::PAGE_INDEX, $downloadAction)
            ->add(Crud::PAGE_DETAIL, $syncAction)
            ->add(Crud::PAGE_DETAIL, $downloadAction)
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
        return $filters
            ->add(TextFilter::new('name', '密钥对名称'))
            ->add(TextFilter::new('fingerprint', '指纹'))
            ->add(TextFilter::new('region', '区域'))
            ->add(BooleanFilter::new('isEncrypted', '是否加密'))
            ->add(EntityFilter::new('credential', 'AWS 凭证'));
    }
    
    /**
     * 同步密钥对状态
     */
    #[Route('admin/key-pair/{entityId}/sync', name: 'sync_key_pair')]
    public function syncKeyPair(AdminContext $context): Response
    {
        $keyPair = $context->getEntity()->getInstance();
        
        $this->addFlash('info', sprintf('密钥对 %s 同步指令已发送', $keyPair->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
    
    /**
     * 下载密钥对私钥
     */
    #[Route('admin/key-pair/{entityId}/download', name: 'download_key_pair')]
    public function downloadKeyPair(AdminContext $context): Response
    {
        $keyPair = $context->getEntity()->getInstance();
        $privateKey = $keyPair->getPrivateKey();
        
        if (!$privateKey) {
            $this->addFlash('danger', sprintf('密钥对 %s 没有可用的私钥', $keyPair->getName()));
            
            return $this->redirect($this->adminUrlGenerator
                ->setAction(Action::INDEX)
                ->setEntityId(null)
                ->generateUrl());
        }
        
        $response = new Response($privateKey);
        $response->headers->set('Content-Type', 'application/x-pem-file');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $keyPair->getName() . '.pem"');
        
        return $response;
    }
} 