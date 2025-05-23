<?php

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Entity\LoadBalancer;
use AwsLightsailBundle\Enum\LoadBalancerStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LoadBalancer>
 *
 * @method LoadBalancer|null find($id, $lockMode = null, $lockVersion = null)
 * @method LoadBalancer|null findOneBy(array $criteria, array $orderBy = null)
 * @method LoadBalancer[]    findAll()
 * @method LoadBalancer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LoadBalancerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LoadBalancer::class);
    }

    /**
     * 按状态查找负载均衡器
     *
     * @param LoadBalancerStatusEnum $status 状态
     * @return LoadBalancer[]
     */
    public function findByStatus(LoadBalancerStatusEnum $status): array
    {
        return $this->createQueryBuilder('lb')
            ->andWhere('lb.status = :status')
            ->setParameter('status', $status)
            ->orderBy('lb.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 按区域查找负载均衡器
     *
     * @param string $region 区域
     * @return LoadBalancer[]
     */
    public function findByRegion(string $region): array
    {
        return $this->createQueryBuilder('lb')
            ->andWhere('lb.region = :region')
            ->setParameter('region', $region)
            ->orderBy('lb.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 按证书名称查找负载均衡器
     *
     * @param string $certificateName 证书名称
     * @return LoadBalancer[]
     */
    public function findByCertificate(string $certificateName): array
    {
        return $this->createQueryBuilder('lb')
            ->andWhere('lb.certificateName = :certificateName')
            ->setParameter('certificateName', $certificateName)
            ->orderBy('lb.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 查找启用了HTTPS的负载均衡器
     *
     * @return LoadBalancer[]
     */
    public function findWithHttpsEnabled(): array
    {
        return $this->createQueryBuilder('lb')
            ->andWhere('lb.httpsEnabled = :httpsEnabled')
            ->setParameter('httpsEnabled', true)
            ->orderBy('lb.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 查找具有特定实例的负载均衡器
     *
     * @param string $instanceName 实例名称
     * @return LoadBalancer[]
     */
    public function findByInstanceName(string $instanceName): array
    {
        $qb = $this->createQueryBuilder('lb');
        return $qb->andWhere($qb->expr()->like('lb.instanceNames', ':instanceName'))
            ->setParameter('instanceName', '%' . $instanceName . '%')
            ->orderBy('lb.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
} 