<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Entity\Disk;
use AwsLightsailBundle\Enum\DiskStateEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<Disk>
 */
#[AsRepository(entityClass: Disk::class)]
class DiskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Disk::class);
    }

    /**
     * 按状态查找磁盘
     *
     * @param DiskStateEnum $state 磁盘状态
     *
     * @return Disk[]
     * @phpstan-return array<int, Disk>
     */
    public function findByState(DiskStateEnum $state): array
    {
        /** @var array<int, Disk> $result */
        $result = $this->createQueryBuilder('d')
            ->andWhere('d.state = :state')
            ->setParameter('state', $state)
            ->orderBy('d.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 按区域查找磁盘
     *
     * @param string $region 区域
     *
     * @return Disk[]
     * @phpstan-return array<int, Disk>
     */
    public function findByRegion(string $region): array
    {
        /** @var array<int, Disk> $result */
        $result = $this->createQueryBuilder('d')
            ->andWhere('d.region = :region')
            ->setParameter('region', $region)
            ->orderBy('d.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 按挂载到的实例名称查找磁盘
     *
     * @param string $instanceName 实例名称
     *
     * @return Disk[]
     * @phpstan-return array<int, Disk>
     */
    public function findByAttachedInstance(string $instanceName): array
    {
        /** @var array<int, Disk> $result */
        $result = $this->createQueryBuilder('d')
            ->andWhere('d.attachedTo = :instanceName')
            ->setParameter('instanceName', $instanceName)
            ->orderBy('d.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 查找未挂载的磁盘
     *
     * @return Disk[]
     * @phpstan-return array<int, Disk>
     */
    public function findDetachedDisks(): array
    {
        /** @var array<int, Disk> $result */
        $result = $this->createQueryBuilder('d')
            ->andWhere('d.attachedTo IS NULL')
            ->orderBy('d.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 查找大于指定大小的磁盘
     *
     * @param int $minSizeInGb 最小大小（GB）
     *
     * @return Disk[]
     * @phpstan-return array<int, Disk>
     */
    public function findLargerThan(int $minSizeInGb): array
    {
        /** @var array<int, Disk> $result */
        $result = $this->createQueryBuilder('d')
            ->andWhere('d.sizeInGb >= :minSize')
            ->setParameter('minSize', $minSizeInGb)
            ->orderBy('d.sizeInGb', 'DESC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    public function save(Disk $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Disk $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
