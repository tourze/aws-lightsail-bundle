<?php

namespace AwsLightsailBundle\Controller\Admin;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Bucket;
use AwsLightsailBundle\Enum\BucketAccessRuleEnum;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Lightsail 存储桶管理控制器
 */
class BucketCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Bucket::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('存储桶')
            ->setEntityLabelInPlural('存储桶列表')
            ->setPageTitle('index', 'Lightsail 存储桶管理')
            ->setPageTitle('new', '创建存储桶')
            ->setPageTitle('edit', fn (Bucket $bucket) => sprintf('编辑存储桶: %s', $bucket->getName()))
            ->setPageTitle('detail', fn (Bucket $bucket) => sprintf('存储桶详情: %s', $bucket->getName()))
            ->setSearchFields(['name', 'region', 'url'])
            ->setDefaultSort(['name' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->hideOnForm()
            ->setMaxLength(9999);
            
        yield TextField::new('name', '存储桶名称');
            
        yield TextField::new('arn', 'AWS ARN')
            ->hideOnForm()
            ->hideOnIndex();
            
        yield UrlField::new('url', 'URL')
            ->hideOnForm();
            
        yield TextField::new('bundleId', '套餐ID')
            ->hideOnForm();
            
        yield TextField::new('region', '区域');
            
        yield ChoiceField::new('accessRules', '访问规则')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => BucketAccessRuleEnum::class
            ])
            ->formatValue(function ($value) {
                return $value instanceof BucketAccessRuleEnum ? $value->getLabel() : '';
            });
            
        yield CodeEditorField::new('readonlyAccessAccounts', '只读访问账户')
            ->hideOnForm()
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            });
            
        yield BooleanField::new('isVersioning', '版本控制')
            ->renderAsSwitch(true);
            
        yield BooleanField::new('objectVersioning', '对象版本控制')
            ->renderAsSwitch(true);
            
        yield BooleanField::new('isResourceType', '资源类型')
            ->hideOnForm();
            
        yield IntegerField::new('sizeInMb', '大小(MB)')
            ->hideOnForm();
            
        yield IntegerField::new('objectCount', '对象数量')
            ->hideOnForm();
            
        yield CodeEditorField::new('tags', '标签')
            ->hideOnForm()
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            });
            
        yield CodeEditorField::new('corsRules', 'CORS规则')
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
            
        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm();
            
        yield DateTimeField::new('syncTime', '同步时间')
            ->hideOnForm();
            
        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm();
    }

    public function configureActions(Actions $actions): Actions
    {
        $syncAction = Action::new('syncBucket', '同步')
            ->linkToCrudAction('syncBucket')
            ->setIcon('fa fa-refresh');
            
        $emptyAction = Action::new('emptyBucket', '清空')
            ->linkToCrudAction('emptyBucket')
            ->setIcon('fa fa-trash')
            ->setCssClass('text-warning');
            
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $syncAction)
            ->add(Crud::PAGE_INDEX, $emptyAction)
            ->add(Crud::PAGE_DETAIL, $syncAction)
            ->add(Crud::PAGE_DETAIL, $emptyAction)
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
        $accessRuleChoices = [];
        foreach (BucketAccessRuleEnum::cases() as $case) {
            $accessRuleChoices[$case->getLabel()] = $case->value;
        }
        
        return $filters
            ->add(TextFilter::new('name', '存储桶名称'))
            ->add(TextFilter::new('region', '区域'))
            ->add(BooleanFilter::new('isVersioning', '版本控制'))
            ->add(BooleanFilter::new('objectVersioning', '对象版本控制'))
            ->add(ChoiceFilter::new('accessRules', '访问规则')->setChoices($accessRuleChoices))
            ->add(EntityFilter::new('credential', 'AWS 凭证'));
    }
    
    /**
     * 同步存储桶状态
     */
    #[Route('admin/bucket/{entityId}/sync', name: 'sync_bucket')]
    public function syncBucket(AdminContext $context): Response
    {
        $bucket = $context->getEntity()->getInstance();
        
        $this->addFlash('info', sprintf('存储桶 %s 同步指令已发送', $bucket->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
    
    /**
     * 清空存储桶
     */
    #[Route('admin/bucket/{entityId}/empty', name: 'empty_bucket')]
    public function emptyBucket(AdminContext $context): Response
    {
        $bucket = $context->getEntity()->getInstance();
        
        $this->addFlash('warning', sprintf('存储桶 %s 清空指令已发送', $bucket->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
}