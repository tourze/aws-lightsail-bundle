<?php

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Entity\Disk;
use AwsLightsailBundle\Entity\DiskSnapshot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DiskSnapshot>
 *
 * @method DiskSnapshot|null find($id, $lockMode = null, $lockVersion = null)
 * @method DiskSnapshot|null findOneBy(array $criteria, array $orderBy = null)
 * @method DiskSnapshot[]    findAll()
 * @method DiskSnapshot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiskSnapshotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DiskSnapshot::class);
    }

    /**
     * 按磁盘查找快照
     *
     * @param Disk $disk 磁盘
     * @return DiskSnapshot[]
     */
    public function findByDisk(Disk $disk): array
    {
        return $this->createQueryBuilder('ds')
            ->andWhere('ds.disk = :disk')
            ->setParameter('disk', $disk)
            ->orderBy('ds.createTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 按磁盘名称查找快照
     *
     * @param string $diskName 磁盘名称
     * @return DiskSnapshot[]
     */
    public function findByDiskName(string $diskName): array
    {
        return $this->createQueryBuilder('ds')
            ->andWhere('ds.diskName = :diskName')
            ->setParameter('diskName', $diskName)
            ->orderBy('ds.createTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 按区域查找磁盘快照
     *
     * @param string $region 区域
     * @return DiskSnapshot[]
     */
    public function findByRegion(string $region): array
    {
        return $this->createQueryBuilder('ds')
            ->andWhere('ds.region = :region')
            ->setParameter('region', $region)
            ->orderBy('ds.createTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 查找自动创建的快照
     *
     * @return DiskSnapshot[]
     */
    public function findAutoSnapshots(): array
    {
        return $this->createQueryBuilder('ds')
            ->andWhere('ds.isFromAutoSnapshot = :isAuto')
            ->setParameter('isAuto', true)
            ->orderBy('ds.createTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 按大小范围查找快照
     *
     * @param int $minSizeInGb 最小大小（GB）
     * @param int|null $maxSizeInGb 最大大小（GB），可选
     * @return DiskSnapshot[]
     */
    public function findBySizeRange(int $minSizeInGb, ?int $maxSizeInGb = null): array
    {
        $qb = $this->createQueryBuilder('ds')
            ->andWhere('ds.sizeInGb >= :minSize')
            ->setParameter('minSize', $minSizeInGb)
            ->orderBy('ds.sizeInGb', 'ASC');
            
        if ($maxSizeInGb !== null) {
            $qb->andWhere('ds.sizeInGb <= :maxSize')
               ->setParameter('maxSize', $maxSizeInGb);
        }
        
        return $qb->getQuery()->getResult();
    }
} 