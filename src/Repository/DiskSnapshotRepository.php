<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Entity\Disk;
use AwsLightsailBundle\Entity\DiskSnapshot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<DiskSnapshot>
 */
#[AsRepository(entityClass: DiskSnapshot::class)]
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
     *
     * @return DiskSnapshot[]
     * @phpstan-return array<int, DiskSnapshot>
     */
    public function findByDisk(Disk $disk): array
    {
        /** @var array<int, DiskSnapshot> $result */
        $result = $this->createQueryBuilder('ds')
            ->andWhere('ds.disk = :disk')
            ->setParameter('disk', $disk)
            ->orderBy('ds.createTime', 'DESC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 按磁盘名称查找快照
     *
     * @param string $diskName 磁盘名称
     *
     * @return DiskSnapshot[]
     * @phpstan-return array<int, DiskSnapshot>
     */
    public function findByDiskName(string $diskName): array
    {
        /** @var array<int, DiskSnapshot> $result */
        $result = $this->createQueryBuilder('ds')
            ->andWhere('ds.diskName = :diskName')
            ->setParameter('diskName', $diskName)
            ->orderBy('ds.createTime', 'DESC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 按区域查找磁盘快照
     *
     * @param string $region 区域
     *
     * @return DiskSnapshot[]
     * @phpstan-return array<int, DiskSnapshot>
     */
    public function findByRegion(string $region): array
    {
        /** @var array<int, DiskSnapshot> $result */
        $result = $this->createQueryBuilder('ds')
            ->andWhere('ds.region = :region')
            ->setParameter('region', $region)
            ->orderBy('ds.createTime', 'DESC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 查找自动创建的快照
     *
     * @return DiskSnapshot[]
     * @phpstan-return array<int, DiskSnapshot>
     */
    public function findAutoSnapshots(): array
    {
        /** @var array<int, DiskSnapshot> $result */
        $result = $this->createQueryBuilder('ds')
            ->andWhere('ds.isFromAutoSnapshot = :isAuto')
            ->setParameter('isAuto', true)
            ->orderBy('ds.createTime', 'DESC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 按大小范围查找快照
     *
     * @param int      $minSizeInGb 最小大小（GB）
     * @param int|null $maxSizeInGb 最大大小（GB），可选
     *
     * @return DiskSnapshot[]
     * @phpstan-return array<int, DiskSnapshot>
     */
    public function findBySizeRange(int $minSizeInGb, ?int $maxSizeInGb = null): array
    {
        $qb = $this->createQueryBuilder('ds')
            ->andWhere('ds.sizeInGb >= :minSize')
            ->setParameter('minSize', $minSizeInGb)
            ->orderBy('ds.sizeInGb', 'ASC')
        ;

        if (null !== $maxSizeInGb) {
            $qb->andWhere('ds.sizeInGb <= :maxSize')
                ->setParameter('maxSize', $maxSizeInGb)
            ;
        }

        /** @var array<int, DiskSnapshot> $result */
        $result = $qb->getQuery()->getResult();
        return $result;
    }

    public function save(DiskSnapshot $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DiskSnapshot $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
