<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Entity\Operation;
use AwsLightsailBundle\Enum\OperationStatusEnum;
use AwsLightsailBundle\Enum\OperationTypeEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<Operation>
 */
#[AsRepository(entityClass: Operation::class)]
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
     *
     * @return Operation[]
     * @phpstan-return array<int, Operation>
     */
    public function findByStatus(OperationStatusEnum $status): array
    {
        /** @var array<int, Operation> $result */
        $result = $this->createQueryBuilder('o')
            ->andWhere('o.status = :status')
            ->setParameter('status', $status)
            ->orderBy('o.createTime', 'DESC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 按类型查找操作
     *
     * @param OperationTypeEnum $type 操作类型
     *
     * @return Operation[]
     * @phpstan-return array<int, Operation>
     */
    public function findByType(OperationTypeEnum $type): array
    {
        /** @var array<int, Operation> $result */
        $result = $this->createQueryBuilder('o')
            ->andWhere('o.type = :type')
            ->setParameter('type', $type)
            ->orderBy('o.createTime', 'DESC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 按资源名称查找操作
     *
     * @param string $resourceName 资源名称
     *
     * @return Operation[]
     * @phpstan-return array<int, Operation>
     */
    public function findByResourceName(string $resourceName): array
    {
        /** @var array<int, Operation> $result */
        $result = $this->createQueryBuilder('o')
            ->andWhere('o.resourceName = :resourceName')
            ->setParameter('resourceName', $resourceName)
            ->orderBy('o.createTime', 'DESC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 按区域查找操作
     *
     * @param string $region 区域
     *
     * @return Operation[]
     * @phpstan-return array<int, Operation>
     */
    public function findByRegion(string $region): array
    {
        /** @var array<int, Operation> $result */
        $result = $this->createQueryBuilder('o')
            ->andWhere('o.region = :region')
            ->setParameter('region', $region)
            ->orderBy('o.createTime', 'DESC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 查找最近的操作
     *
     * @param int $limit 限制数量
     *
     * @return Operation[]
     * @phpstan-return array<int, Operation>
     */
    public function findRecent(int $limit = 10): array
    {
        /** @var array<int, Operation> $result */
        $result = $this->createQueryBuilder('o')
            ->orderBy('o.createTime', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    public function save(Operation $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Operation $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
