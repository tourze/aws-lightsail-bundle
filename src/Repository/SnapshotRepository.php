<?php

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Entity\Snapshot;
use AwsLightsailBundle\Enum\SnapshotTypeEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Snapshot>
 *
 * @method Snapshot|null find($id, $lockMode = null, $lockVersion = null)
 * @method Snapshot|null findOneBy(array $criteria, array $orderBy = null)
 * @method Snapshot[]    findAll()
 * @method Snapshot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
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
     * @return Snapshot[]
     */
    public function findByType(SnapshotTypeEnum $type): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.type = :type')
            ->setParameter('type', $type)
            ->orderBy('s.createTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 按资源名称查找快照
     *
     * @param string $resourceName 资源名称
     * @return Snapshot[]
     */
    public function findByResourceName(string $resourceName): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.resourceName = :resourceName')
            ->setParameter('resourceName', $resourceName)
            ->orderBy('s.createTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 按区域查找快照
     *
     * @param string $region 区域
     * @return Snapshot[]
     */
    public function findByRegion(string $region): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.region = :region')
            ->setParameter('region', $region)
            ->orderBy('s.createTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 查找自动创建的快照
     *
     * @return Snapshot[]
     */
    public function findAutoSnapshots(): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.isFromAutoSnapshot = :isAuto')
            ->setParameter('isAuto', true)
            ->orderBy('s.createTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 按日期范围查找快照
     *
     * @param \DateTimeInterface $fromDate 开始日期
     * @param \DateTimeInterface|null $toDate 结束日期，默认为当前时间
     * @return Snapshot[]
     */
    public function findByDateRange(\DateTimeInterface $fromDate, ?\DateTimeInterface $toDate = null): array
    {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.createTime >= :fromDate')
            ->setParameter('fromDate', $fromDate)
            ->orderBy('s.createTime', 'DESC');

        if ($toDate !== null) {
            $qb->andWhere('s.createTime <= :toDate')
               ->setParameter('toDate', $toDate);
        }

        return $qb->getQuery()->getResult();
    }
} 