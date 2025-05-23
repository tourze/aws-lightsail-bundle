<?php

namespace AwsLightsailBundle\Service;

use AwsLightsailBundle\Entity\Alarm;
use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Bucket;
use AwsLightsailBundle\Entity\Certificate;
use AwsLightsailBundle\Entity\ContactMethod;
use AwsLightsailBundle\Entity\ContainerService;
use AwsLightsailBundle\Entity\Database;
use AwsLightsailBundle\Entity\DatabaseSnapshot;
use AwsLightsailBundle\Entity\Disk;
use AwsLightsailBundle\Entity\DiskSnapshot;
use AwsLightsailBundle\Entity\Distribution;
use AwsLightsailBundle\Entity\Domain;
use AwsLightsailBundle\Entity\Instance;
use AwsLightsailBundle\Entity\KeyPair;
use AwsLightsailBundle\Entity\LoadBalancer;
use AwsLightsailBundle\Entity\Operation;
use AwsLightsailBundle\Entity\Snapshot;
use AwsLightsailBundle\Entity\StaticIp;
use Knp\Menu\ItemInterface;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

/**
 * AWS Lightsail 菜单服务
 */
class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private readonly LinkGeneratorInterface $linkGenerator,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        // 创建主菜单项 AWS Lightsail
        if (!$item->getChild('AWS Lightsail')) {
            $item->addChild('AWS Lightsail')
                ->setAttribute('icon', 'fa fa-cloud');
        }

        $awsMenu = $item->getChild('AWS Lightsail');

        $awsMenu->addChild('AWS 凭证')->setUri($this->linkGenerator->getCurdListPage(AwsCredential::class))->setAttribute('icon', 'fa fa-key');
        $awsMenu->addChild('实例')->setUri($this->linkGenerator->getCurdListPage(Instance::class))->setAttribute('icon', 'fa fa-server');
        $awsMenu->addChild('快照')->setUri($this->linkGenerator->getCurdListPage(Snapshot::class))->setAttribute('icon', 'fa fa-camera');
        $awsMenu->addChild('密钥对')->setUri($this->linkGenerator->getCurdListPage(KeyPair::class))->setAttribute('icon', 'fa fa-lock');
        $awsMenu->addChild('静态IP')->setUri($this->linkGenerator->getCurdListPage(StaticIp::class))->setAttribute('icon', 'fa fa-globe');
        $awsMenu->addChild('磁盘')->setUri($this->linkGenerator->getCurdListPage(Disk::class))->setAttribute('icon', 'fa fa-hdd');
        $awsMenu->addChild('磁盘快照')->setUri($this->linkGenerator->getCurdListPage(DiskSnapshot::class))->setAttribute('icon', 'fa fa-camera');
        $awsMenu->addChild('存储桶')->setUri($this->linkGenerator->getCurdListPage(Bucket::class))->setAttribute('icon', 'fa fa-database');
        $awsMenu->addChild('域名')->setUri($this->linkGenerator->getCurdListPage(Domain::class))->setAttribute('icon', 'fa fa-globe');
        $awsMenu->addChild('CDN分发')->setUri($this->linkGenerator->getCurdListPage(Distribution::class))->setAttribute('icon', 'fa fa-sitemap');
        $awsMenu->addChild('负载均衡器')->setUri($this->linkGenerator->getCurdListPage(LoadBalancer::class))->setAttribute('icon', 'fa fa-balance-scale');
        $awsMenu->addChild('证书')->setUri($this->linkGenerator->getCurdListPage(Certificate::class))->setAttribute('icon', 'fa fa-certificate');
        $awsMenu->addChild('数据库')->setUri($this->linkGenerator->getCurdListPage(Database::class))->setAttribute('icon', 'fa fa-database');
        $awsMenu->addChild('数据库快照')->setUri($this->linkGenerator->getCurdListPage(DatabaseSnapshot::class))->setAttribute('icon', 'fa fa-camera');
        $awsMenu->addChild('容器服务')->setUri($this->linkGenerator->getCurdListPage(ContainerService::class))->setAttribute('icon', 'fa fa-docker');
        $awsMenu->addChild('告警')->setUri($this->linkGenerator->getCurdListPage(Alarm::class))->setAttribute('icon', 'fa fa-bell');
        $awsMenu->addChild('联系方式')->setUri($this->linkGenerator->getCurdListPage(ContactMethod::class))->setAttribute('icon', 'fa fa-address-book');
        $awsMenu->addChild('操作记录')->setUri($this->linkGenerator->getCurdListPage(Operation::class))->setAttribute('icon', 'fa fa-history');
    }
}
