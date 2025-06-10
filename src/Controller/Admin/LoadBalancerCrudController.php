<?php

namespace AwsLightsailBundle\Controller\Admin;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\LoadBalancer;
use AwsLightsailBundle\Enum\LoadBalancerStatusEnum;
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
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Lightsail 负载均衡器管理控制器
 */
class LoadBalancerCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return LoadBalancer::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('负载均衡器')
            ->setEntityLabelInPlural('负载均衡器列表')
            ->setPageTitle('index', 'Lightsail 负载均衡器管理')
            ->setPageTitle('new', '创建负载均衡器')
            ->setPageTitle('edit', fn (LoadBalancer $loadBalancer) => sprintf('编辑负载均衡器: %s', $loadBalancer->getName()))
            ->setPageTitle('detail', fn (LoadBalancer $loadBalancer) => sprintf('负载均衡器详情: %s', $loadBalancer->getName()))
            ->setSearchFields(['name', 'dnsName', 'region'])
            ->setDefaultSort(['name' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->hideOnForm()
            ->setMaxLength(9999);
            
        yield TextField::new('name', '负载均衡器名称');
            
        yield TextField::new('arn', 'AWS ARN')
            ->hideOnForm()
            ->hideOnIndex();
            
        yield TextField::new('dnsName', 'DNS 名称')
            ->setHelp('负载均衡器的 DNS 名称');
            
        yield TextField::new('region', '区域');
            
        yield IntegerField::new('healthCheckPort', '健康检查端口');
            
        yield TextField::new('healthCheckProtocol', '健康检查协议')
            ->hideOnIndex();
            
        yield TextField::new('healthCheckPath', '健康检查路径')
            ->hideOnIndex();
            
        yield IntegerField::new('healthCheckIntervalSeconds', '健康检查间隔(秒)')
            ->hideOnIndex();
            
        yield IntegerField::new('healthCheckTimeoutSeconds', '健康检查超时(秒)')
            ->hideOnIndex();
            
        yield IntegerField::new('healthyThreshold', '健康阈值')
            ->hideOnIndex();
            
        yield IntegerField::new('unhealthyThreshold', '不健康阈值')
            ->hideOnIndex();
            
        yield ChoiceField::new('status', '状态')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => LoadBalancerStatusEnum::class
            ])
            ->formatValue(function ($value) {
                return $value instanceof LoadBalancerStatusEnum ? $value->getLabel() : '';
            });
            
        yield BooleanField::new('tlsPolicyEnabled', 'TLS 策略启用')
            ->renderAsSwitch(true);
            
        yield TextField::new('tlsCertificateName', 'TLS 证书名称')
            ->hideOnIndex();
            
        yield CodeEditorField::new('instanceHealthSummary', '实例健康摘要')
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
            
        yield CodeEditorField::new('attachedInstances', '已附加实例')
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            });
            
        yield BooleanField::new('configurationOptions', '配置选项')
            ->renderAsSwitch(true);
            
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
        $syncAction = Action::new('syncLoadBalancer', '同步')
            ->linkToCrudAction('syncLoadBalancer')
            ->setIcon('fa fa-refresh');
            
        $attachAction = Action::new('attachInstance', '附加实例')
            ->linkToCrudAction('attachInstance')
            ->setIcon('fa fa-link')
            ->setCssClass('text-success');
            
        $detachAction = Action::new('detachInstance', '分离实例')
            ->linkToCrudAction('detachInstance')
            ->setIcon('fa fa-unlink')
            ->setCssClass('text-danger');
            
        $updateCertAction = Action::new('updateCertificate', '更新证书')
            ->linkToCrudAction('updateCertificate')
            ->setIcon('fa fa-certificate')
            ->setCssClass('text-primary');
            
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $syncAction)
            ->add(Crud::PAGE_INDEX, $attachAction)
            ->add(Crud::PAGE_INDEX, $detachAction)
            ->add(Crud::PAGE_INDEX, $updateCertAction)
            ->add(Crud::PAGE_DETAIL, $syncAction)
            ->add(Crud::PAGE_DETAIL, $attachAction)
            ->add(Crud::PAGE_DETAIL, $detachAction)
            ->add(Crud::PAGE_DETAIL, $updateCertAction)
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
        foreach (LoadBalancerStatusEnum::cases() as $case) {
            $statusChoices[$case->getLabel()] = $case->value;
        }
        
        return $filters
            ->add(TextFilter::new('name', '负载均衡器名称'))
            ->add(TextFilter::new('dnsName', 'DNS 名称'))
            ->add(TextFilter::new('region', '区域'))
            ->add(ChoiceFilter::new('status', '状态')->setChoices($statusChoices))
            ->add(BooleanFilter::new('tlsPolicyEnabled', 'TLS 策略启用'))
            ->add(TextFilter::new('tlsCertificateName', 'TLS 证书名称'))
            ->add(EntityFilter::new('credential', 'AWS 凭证'));
    }
    
    /**
     * 同步负载均衡器状态
     */
    #[Route('admin/load-balancer/{entityId}/sync', name: 'sync_load_balancer')]
    public function syncLoadBalancer(AdminContext $context): Response
    {
        $loadBalancer = $context->getEntity()->getInstance();
        
        $this->addFlash('info', sprintf('负载均衡器 %s 同步指令已发送', $loadBalancer->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
    
    /**
     * 附加实例到负载均衡器
     */
    #[Route('admin/load-balancer/{entityId}/attach-instance', name: 'attach_load_balancer_instance')]
    public function attachInstance(AdminContext $context): Response
    {
        $loadBalancer = $context->getEntity()->getInstance();
        
        $this->addFlash('success', sprintf('负载均衡器 %s 附加实例指令已发送', $loadBalancer->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
    
    /**
     * 从负载均衡器分离实例
     */
    #[Route('admin/load-balancer/{entityId}/detach-instance', name: 'detach_load_balancer_instance')]
    public function detachInstance(AdminContext $context): Response
    {
        $loadBalancer = $context->getEntity()->getInstance();
        
        $this->addFlash('warning', sprintf('负载均衡器 %s 分离实例指令已发送', $loadBalancer->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
    
    /**
     * 更新负载均衡器证书
     */
    #[Route('admin/load-balancer/{entityId}/update-certificate', name: 'update_load_balancer_certificate')]
    public function updateCertificate(AdminContext $context): Response
    {
        $loadBalancer = $context->getEntity()->getInstance();
        
        $this->addFlash('info', sprintf('负载均衡器 %s 更新证书指令已发送', $loadBalancer->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
} 