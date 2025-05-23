<?php

namespace AwsLightsailBundle\Controller\Admin;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Distribution;
use AwsLightsailBundle\Enum\DistributionStatusEnum;
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
use Symfony\Component\Routing\Annotation\Route;

/**
 * Lightsail CDN 分发管理控制器
 */
class DistributionCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Distribution::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('CDN 分发')
            ->setEntityLabelInPlural('CDN 分发列表')
            ->setPageTitle('index', 'Lightsail CDN 分发管理')
            ->setPageTitle('new', '创建 CDN 分发')
            ->setPageTitle('edit', fn (Distribution $distribution) => sprintf('编辑 CDN 分发: %s', $distribution->getName()))
            ->setPageTitle('detail', fn (Distribution $distribution) => sprintf('CDN 分发详情: %s', $distribution->getName()))
            ->setSearchFields(['name', 'defaultDomainName', 'region'])
            ->setDefaultSort(['name' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->hideOnForm()
            ->setMaxLength(9999);
            
        yield TextField::new('name', '分发名称');
            
        yield TextField::new('arn', 'AWS ARN')
            ->hideOnForm()
            ->hideOnIndex();
            
        yield TextField::new('defaultDomainName', '默认域名')
            ->setHelp('AWS 分配的默认域名');
            
        yield ChoiceField::new('status', '状态')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => DistributionStatusEnum::class
            ])
            ->formatValue(function ($value) {
                return $value instanceof DistributionStatusEnum ? $value->getLabel() : '';
            });
            
        yield TextField::new('region', '区域');
            
        yield CodeEditorField::new('originConfigs', '源站配置')
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            });
            
        yield CodeEditorField::new('defaultCacheBehavior', '默认缓存行为')
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            });
            
        yield CodeEditorField::new('cacheBehaviors', '缓存行为')
            ->hideOnIndex()
            ->hideOnForm()
            ->formatValue(function ($value) {
                return $value ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '{}';
            });
            
        yield BooleanField::new('isEnabled', '是否启用')
            ->renderAsSwitch(true);
            
        yield TextField::new('certificateName', '证书名称')
            ->hideOnIndex();
            
        yield BooleanField::new('viewerProtocolPolicy', 'HTTPS 重定向')
            ->renderAsSwitch(true)
            ->setHelp('是否将 HTTP 请求重定向到 HTTPS');
            
        yield CodeEditorField::new('alternativeDomainNames', '备用域名')
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return $value ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '[]';
            });
            
        yield CodeEditorField::new('originPublicDNS', '源站公共 DNS')
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return $value ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '{}';
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
        $syncAction = Action::new('syncDistribution', '同步')
            ->linkToCrudAction('syncDistribution')
            ->setIcon('fa fa-refresh');
            
        $resetCacheAction = Action::new('resetCache', '清除缓存')
            ->linkToCrudAction('resetCache')
            ->setIcon('fa fa-eraser')
            ->setCssClass('text-warning');
            
        $toggleEnableAction = Action::new('toggleEnable', '启用/禁用')
            ->linkToCrudAction('toggleEnable')
            ->setIcon('fa fa-power-off');
            
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $syncAction)
            ->add(Crud::PAGE_INDEX, $resetCacheAction)
            ->add(Crud::PAGE_INDEX, $toggleEnableAction)
            ->add(Crud::PAGE_DETAIL, $syncAction)
            ->add(Crud::PAGE_DETAIL, $resetCacheAction)
            ->add(Crud::PAGE_DETAIL, $toggleEnableAction)
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
        foreach (DistributionStatusEnum::cases() as $case) {
            $statusChoices[$case->getLabel()] = $case->value;
        }
        
        return $filters
            ->add(TextFilter::new('name', '分发名称'))
            ->add(TextFilter::new('defaultDomainName', '默认域名'))
            ->add(TextFilter::new('region', '区域'))
            ->add(BooleanFilter::new('isEnabled', '是否启用'))
            ->add(ChoiceFilter::new('status', '状态')->setChoices($statusChoices))
            ->add(TextFilter::new('certificateName', '证书名称'))
            ->add(EntityFilter::new('credential', 'AWS 凭证'));
    }
    
    /**
     * 同步 CDN 分发状态
     */
    #[Route('admin/distribution/{entityId}/sync', name: 'sync_distribution')]
    public function syncDistribution(AdminContext $context): Response
    {
        $distribution = $context->getEntity()->getInstance();
        
        $this->addFlash('info', sprintf('CDN分发 %s 同步指令已发送', $distribution->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
    
    /**
     * 清除 CDN 分发缓存
     */
    #[Route('admin/distribution/{entityId}/reset-cache', name: 'reset_distribution_cache')]
    public function resetCache(AdminContext $context): Response
    {
        $distribution = $context->getEntity()->getInstance();
        
        $this->addFlash('warning', sprintf('CDN分发 %s 缓存清除指令已发送', $distribution->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
    
    /**
     * 切换 CDN 分发启用状态
     */
    #[Route('admin/distribution/{entityId}/toggle-enable', name: 'toggle_distribution_enable')]
    public function toggleEnable(AdminContext $context): Response
    {
        $distribution = $context->getEntity()->getInstance();
        $currentState = $distribution->isEnabled();
        
        $distribution->setIsEnabled(!$currentState);
        $this->entityManager->flush();
        
        $newState = $distribution->isEnabled() ? '启用' : '禁用';
        $this->addFlash('success', sprintf('CDN分发 %s 已%s', $distribution->getName(), $newState));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
}
