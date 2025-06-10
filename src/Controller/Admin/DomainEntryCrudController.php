<?php

namespace AwsLightsailBundle\Controller\Admin;

use AwsLightsailBundle\Entity\Domain;
use AwsLightsailBundle\Entity\DomainEntry;
use AwsLightsailBundle\Enum\DnsRecordTypeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
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
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Lightsail 域名记录管理控制器
 */
class DomainEntryCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AdminUrlGenerator $adminUrlGenerator,
        private readonly RequestStack $requestStack
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return DomainEntry::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('域名记录')
            ->setEntityLabelInPlural('域名记录列表')
            ->setPageTitle('index', 'Lightsail 域名记录管理')
            ->setPageTitle('new', '创建域名记录')
            ->setPageTitle('edit', fn (DomainEntry $entry) => sprintf('编辑域名记录: %s', $entry->getName()))
            ->setPageTitle('detail', fn (DomainEntry $entry) => sprintf('域名记录详情: %s', $entry->getName()))
            ->setSearchFields(['name', 'type', 'value'])
            ->setDefaultSort(['name' => 'ASC']);
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        
        // 如果URL参数中有domain_id，则过滤该域名下的记录
        $request = $this->requestStack->getCurrentRequest();
        if ($request && $domainId = $request->query->get('domain_id')) {
            $queryBuilder
                ->andWhere('entity.domain = :domain_id')
                ->setParameter('domain_id', $domainId);
        }
        
        return $queryBuilder;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->hideOnForm()
            ->setMaxLength(9999);
            
        yield TextField::new('name', '记录名称');
            
        yield ChoiceField::new('type', '记录类型')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => DnsRecordTypeEnum::class
            ])
            ->formatValue(function ($value) {
                return $value instanceof DnsRecordTypeEnum ? $value->getLabel() : '';
            });
            
        yield TextField::new('value', '记录值');
            
        yield IntegerField::new('ttl', 'TTL')
            ->setHelp('生存时间，单位为秒');
            
        yield IntegerField::new('priority', '优先级')
            ->hideOnIndex()
            ->setHelp('仅对MX和SRV记录有效');
            
        yield BooleanField::new('isAlias', '是否别名')
            ->renderAsSwitch(true);
            
        $domainField = AssociationField::new('domain', '所属域名')
            ->formatValue(function ($value) {
                return $value instanceof Domain ? $value->getName() : '';
            });
            
        // 在 "新建" 页面获取domain_id参数并设置默认值
        if ($pageName === Crud::PAGE_NEW) {
            $request = $this->requestStack->getCurrentRequest();
            if ($request && $domainId = $request->query->get('domain_id')) {
                $domain = $this->entityManager->getRepository(Domain::class)->find($domainId);
                if ($domain) {
                    $domainField->setFormTypeOption('data', $domain);
                }
            }
        }
        
        yield $domainField;
            
        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm();
            
        yield DateTimeField::new('syncTime', '同步时间')
            ->hideOnForm();
            
        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm();
    }

    public function configureActions(Actions $actions): Actions
    {
        $syncAction = Action::new('syncDomainEntry', '同步')
            ->linkToCrudAction('syncDomainEntry')
            ->setIcon('fa fa-refresh');
            
        $backToDomainAction = Action::new('backToDomain', '返回域名')
            ->linkToCrudAction('backToDomain')
            ->setIcon('fa fa-arrow-left')
            ->setCssClass('text-primary');
            
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $syncAction)
            ->add(Crud::PAGE_INDEX, $backToDomainAction)
            ->add(Crud::PAGE_DETAIL, $syncAction)
            ->add(Crud::PAGE_DETAIL, $backToDomainAction)
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
        foreach (DnsRecordTypeEnum::cases() as $case) {
            $typeChoices[$case->getLabel()] = $case->value;
        }
        
        return $filters
            ->add(TextFilter::new('name', '记录名称'))
            ->add(TextFilter::new('value', '记录值'))
            ->add(ChoiceFilter::new('type', '记录类型')->setChoices($typeChoices))
            ->add(BooleanFilter::new('isAlias', '是否别名'))
            ->add(EntityFilter::new('domain', '所属域名'));
    }
    
    /**
     * 同步域名记录
     */
    #[Route('admin/domain-entry/{entityId}/sync', name: 'sync_domain_entry')]
    public function syncDomainEntry(AdminContext $context): Response
    {
        $entry = $context->getEntity()->getInstance();
        
        $this->addFlash('info', sprintf('域名记录 %s 同步指令已发送', $entry->getName()));
        
        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl());
    }
    
    /**
     * 返回到域名详情页
     */
    #[Route('admin/domain-entry/back-to-domain', name: 'back_to_domain_entry')]
    public function backToDomain(AdminContext $context): Response
    {
        // 如果是实体上下文，获取关联的域名
        if ($context->getEntity()) {
            $entry = $context->getEntity()->getInstance();
            $domain = $entry->getDomain();
            
            return $this->redirect($this->adminUrlGenerator
                ->setController(DomainCrudController::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($domain->getId())
                ->generateUrl());
        }
        
        // 否则返回到域名列表
        return $this->redirect($this->adminUrlGenerator
            ->setController(DomainCrudController::class)
            ->setAction(Action::INDEX)
            ->generateUrl());
    }
} 