<?php

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Entity\ContainerService;
use AwsLightsailBundle\Enum\ContainerServicePowerEnum;
use AwsLightsailBundle\Enum\ContainerServiceStateEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ContainerService>
 *
 * @method ContainerService|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContainerService|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContainerService[]    findAll()
 * @method ContainerService[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContainerServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContainerService::class);
    }

    /**
     * 按状态查找容器服务
     *
     * @param ContainerServiceStateEnum $state 状态
     * @return ContainerService[]
     */
    public function findByState(ContainerServiceStateEnum $state): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.state = :state')
            ->setParameter('state', $state)
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 按容器性能等级查找
     *
     * @param ContainerServicePowerEnum $power 性能等级
     * @return ContainerService[]
     */
    public function findByPower(ContainerServicePowerEnum $power): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.power = :power')
            ->setParameter('power', $power)
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 按区域查找容器服务
     *
     * @param string $region 区域
     * @return ContainerService[]
     */
    public function findByRegion(string $region): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.region = :region')
            ->setParameter('region', $region)
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 查找至少有指定数量副本的容器服务
     *
     * @param int $minScale 最小副本数
     * @return ContainerService[]
     */
    public function findByMinimumScale(int $minScale): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.scale >= :minScale')
            ->setParameter('minScale', $minScale)
            ->orderBy('c.scale', 'DESC')
            ->getQuery()
            ->getResult();
    }
} 