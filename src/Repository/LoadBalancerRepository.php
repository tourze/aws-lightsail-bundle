<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Entity\LoadBalancer;
use AwsLightsailBundle\Enum\LoadBalancerStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<LoadBalancer>
 */
#[AsRepository(entityClass: LoadBalancer::class)]
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
     *
     * @return LoadBalancer[]
     * @phpstan-return array<int, LoadBalancer>
     */
    public function findByStatus(LoadBalancerStatusEnum $status): array
    {
        /** @var array<int, LoadBalancer> $result */
        $result = $this->createQueryBuilder('lb')
            ->andWhere('lb.status = :status')
            ->setParameter('status', $status)
            ->orderBy('lb.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 按区域查找负载均衡器
     *
     * @param string $region 区域
     *
     * @return LoadBalancer[]
     * @phpstan-return array<int, LoadBalancer>
     */
    public function findByRegion(string $region): array
    {
        /** @var array<int, LoadBalancer> $result */
        $result = $this->createQueryBuilder('lb')
            ->andWhere('lb.region = :region')
            ->setParameter('region', $region)
            ->orderBy('lb.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 按证书名称查找负载均衡器
     *
     * @param string $certificateName 证书名称
     *
     * @return LoadBalancer[]
     * @phpstan-return array<int, LoadBalancer>
     */
    public function findByCertificate(string $certificateName): array
    {
        /** @var array<int, LoadBalancer> $result */
        $result = $this->createQueryBuilder('lb')
            ->andWhere('lb.tlsCertificateName = :certificateName')
            ->setParameter('certificateName', $certificateName)
            ->orderBy('lb.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 查找启用了HTTPS的负载均衡器
     *
     * @return LoadBalancer[]
     * @phpstan-return array<int, LoadBalancer>
     */
    public function findWithHttpsEnabled(): array
    {
        /** @var array<int, LoadBalancer> $result */
        $result = $this->createQueryBuilder('lb')
            ->andWhere('lb.tlsPolicyEnabled = :httpsEnabled')
            ->setParameter('httpsEnabled', true)
            ->orderBy('lb.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 查找具有特定实例的负载均衡器
     *
     * @param string $instanceName 实例名称
     *
     * @return LoadBalancer[]
     * @phpstan-return array<int, LoadBalancer>
     */
    public function findByInstanceName(string $instanceName): array
    {
        $qb = $this->createQueryBuilder('lb');

        /** @var array<int, LoadBalancer> $result */
        $result = $qb->andWhere($qb->expr()->like('lb.attachedInstances', ':instanceName'))
            ->setParameter('instanceName', '%"' . $instanceName . '"%')
            ->orderBy('lb.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    public function save(LoadBalancer $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(LoadBalancer $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
