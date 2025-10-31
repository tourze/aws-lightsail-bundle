<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Entity\Snapshot;
use AwsLightsailBundle\Enum\SnapshotTypeEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<Snapshot>
 */
#[AsRepository(entityClass: Snapshot::class)]
class SnapshotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Snapshot::class);
    }

    /**
     * 按类型查找快照
     *
     * @param SnapshotTypeEnum $type 快照类型
     *
     * @return Snapshot[]
     * @phpstan-return array<int, Snapshot>
     */
    public function findByType(SnapshotTypeEnum $type): array
    {
        /** @var array<int, Snapshot> $result */
        $result = $this->createQueryBuilder('s')
            ->andWhere('s.type = :type')
            ->setParameter('type', $type)
            ->orderBy('s.createTime', 'DESC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 按资源名称查找快照
     *
     * @param string $resourceName 资源名称
     *
     * @return Snapshot[]
     * @phpstan-return array<int, Snapshot>
     */
    public function findByResourceName(string $resourceName): array
    {
        /** @var array<int, Snapshot> $result */
        $result = $this->createQueryBuilder('s')
            ->andWhere('s.resourceName = :resourceName')
            ->setParameter('resourceName', $resourceName)
            ->orderBy('s.createTime', 'DESC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 按区域查找快照
     *
     * @param string $region 区域
     *
     * @return Snapshot[]
     * @phpstan-return array<int, Snapshot>
     */
    public function findByRegion(string $region): array
    {
        /** @var array<int, Snapshot> $result */
        $result = $this->createQueryBuilder('s')
            ->andWhere('s.region = :region')
            ->setParameter('region', $region)
            ->orderBy('s.createTime', 'DESC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 查找自动创建的快照
     *
     * @return Snapshot[]
     * @phpstan-return array<int, Snapshot>
     */
    public function findAutoSnapshots(): array
    {
        /** @var array<int, Snapshot> $result */
        $result = $this->createQueryBuilder('s')
            ->andWhere('s.isFromAutoSnapshot = :isAuto')
            ->setParameter('isAuto', true)
            ->orderBy('s.createTime', 'DESC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 按日期范围查找快照
     *
     * @param \DateTimeInterface      $fromDate 开始日期
     * @param \DateTimeInterface|null $toDate   结束日期，默认为当前时间
     *
     * @return Snapshot[]
     * @phpstan-return array<int, Snapshot>
     */
    public function findByDateRange(\DateTimeInterface $fromDate, ?\DateTimeInterface $toDate = null): array
    {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.createTime >= :fromDate')
            ->setParameter('fromDate', $fromDate)
            ->orderBy('s.createTime', 'DESC')
        ;

        if (null !== $toDate) {
            $qb->andWhere('s.createTime <= :toDate')
                ->setParameter('toDate', $toDate)
            ;
        }

        /** @var array<int, Snapshot> $result */
        $result = $qb->getQuery()->getResult();
        return $result;
    }

    public function save(Snapshot $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Snapshot $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
