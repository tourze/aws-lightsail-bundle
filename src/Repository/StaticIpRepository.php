<?php

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Entity\Instance;
use AwsLightsailBundle\Entity\StaticIp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StaticIp>
 *
 * @method StaticIp|null find($id, $lockMode = null, $lockVersion = null)
 * @method StaticIp|null findOneBy(array $criteria, array $orderBy = null)
 * @method StaticIp[]    findAll()
 * @method StaticIp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StaticIpRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StaticIp::class);
    }

    /**
     * 按区域查找静态IP
     *
     * @param string $region 区域
     * @return StaticIp[]
     */
    public function findByRegion(string $region): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.region = :region')
            ->setParameter('region', $region)
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 查找已分配给实例的静态IP
     *
     * @return StaticIp[]
     */
    public function findAttached(): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.instanceName IS NOT NULL')
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 查找未分配的静态IP
     *
     * @return StaticIp[]
     */
    public function findDetached(): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.instanceName IS NULL')
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 按实例查找静态IP
     *
     * @param Instance $instance 实例
     * @return StaticIp|null
     */
    public function findByInstance(Instance $instance): ?StaticIp
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.instanceName = :instanceName')
            ->setParameter('instanceName', $instance->getName())
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * 按IP地址查找静态IP
     *
     * @param string $ipAddress IP地址
     * @return StaticIp|null
     */
    public function findByIpAddress(string $ipAddress): ?StaticIp
    {
        return $this->findOneBy(['ipAddress' => $ipAddress]);
    }
} 