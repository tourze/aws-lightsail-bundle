<?php

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Entity\Operation;
use AwsLightsailBundle\Enum\OperationStatusEnum;
use AwsLightsailBundle\Enum\OperationTypeEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Operation>
 *
 * @method Operation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Operation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Operation[]    findAll()
 * @method Operation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OperationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Operation::class);
    }

    /**
     * 按状态查找操作
     *
     * @param OperationStatusEnum $status 状态
     * @return Operation[]
     */
    public function findByStatus(OperationStatusEnum $status): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.status = :status')
            ->setParameter('status', $status)
            ->orderBy('o.createTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 按类型查找操作
     *
     * @param OperationTypeEnum $type 操作类型
     * @return Operation[]
     */
    public function findByType(OperationTypeEnum $type): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.type = :type')
            ->setParameter('type', $type)
            ->orderBy('o.createTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 按资源名称查找操作
     *
     * @param string $resourceName 资源名称
     * @return Operation[]
     */
    public function findByResourceName(string $resourceName): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.resourceName = :resourceName')
            ->setParameter('resourceName', $resourceName)
            ->orderBy('o.createTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 按区域查找操作
     *
     * @param string $region 区域
     * @return Operation[]
     */
    public function findByRegion(string $region): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.region = :region')
            ->setParameter('region', $region)
            ->orderBy('o.createTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 查找最近的操作
     *
     * @param int $limit 限制数量
     * @return Operation[]
     */
    public function findRecent(int $limit = 10): array
    {
        return $this->createQueryBuilder('o')
            ->orderBy('o.createTime', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
} 