<?php

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Entity\Instance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Instance>
 *
 * @method Instance|null find($id, $lockMode = null, $lockVersion = null)
 * @method Instance|null findOneBy(array $criteria, array $orderBy = null)
 * @method Instance[]    findAll()
 * @method Instance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InstanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Instance::class);
    }

    /**
     * 按实例状态查找
     *
     * @param string $state 实例状态
     * @return Instance[]
     */
    public function findByState(string $state): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.state = :state')
            ->setParameter('state', $state)
            ->orderBy('i.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 按区域查找
     *
     * @param string $region 区域
     * @return Instance[]
     */
    public function findByRegion(string $region): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.region = :region')
            ->setParameter('region', $region)
            ->orderBy('i.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 按蓝图类型查找
     *
     * @param string $blueprint 蓝图类型
     * @return Instance[]
     */
    public function findByBlueprint(string $blueprint): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.blueprint = :blueprint')
            ->setParameter('blueprint', $blueprint)
            ->orderBy('i.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
