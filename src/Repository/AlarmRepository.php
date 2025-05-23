<?php

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Entity\Alarm;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Alarm>
 *
 * @method Alarm|null find($id, $lockMode = null, $lockVersion = null)
 * @method Alarm|null findOneBy(array $criteria, array $orderBy = null)
 * @method Alarm[]    findAll()
 * @method Alarm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlarmRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Alarm::class);
    }

    /**
     * 按资源名称查找告警
     *
     * @param string $resourceName 资源名称
     * @return Alarm[]
     */
    public function findByResourceName(string $resourceName): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.resourceName = :resourceName')
            ->setParameter('resourceName', $resourceName)
            ->getQuery()
            ->getResult();
    }

    /**
     * 按资源类型查找告警
     *
     * @param string $resourceType 资源类型
     * @return Alarm[]
     */
    public function findByResourceType(string $resourceType): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.resourceType = :resourceType')
            ->setParameter('resourceType', $resourceType)
            ->getQuery()
            ->getResult();
    }

    /**
     * 按状态查找告警
     *
     * @param string $state 告警状态
     * @return Alarm[]
     */
    public function findByState(string $state): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.state = :state')
            ->setParameter('state', $state)
            ->getQuery()
            ->getResult();
    }
} 