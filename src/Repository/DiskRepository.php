<?php

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Entity\Disk;
use AwsLightsailBundle\Enum\DiskStateEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Disk>
 *
 * @method Disk|null find($id, $lockMode = null, $lockVersion = null)
 * @method Disk|null findOneBy(array $criteria, array $orderBy = null)
 * @method Disk[]    findAll()
 * @method Disk[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
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
     * @return Disk[]
     */
    public function findByState(DiskStateEnum $state): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.state = :state')
            ->setParameter('state', $state)
            ->orderBy('d.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 按区域查找磁盘
     *
     * @param string $region 区域
     * @return Disk[]
     */
    public function findByRegion(string $region): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.region = :region')
            ->setParameter('region', $region)
            ->orderBy('d.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 按挂载到的实例名称查找磁盘
     *
     * @param string $instanceName 实例名称
     * @return Disk[]
     */
    public function findByAttachedInstance(string $instanceName): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.attachedTo = :instanceName')
            ->setParameter('instanceName', $instanceName)
            ->orderBy('d.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 查找未挂载的磁盘
     *
     * @return Disk[]
     */
    public function findDetachedDisks(): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.attachedTo IS NULL')
            ->orderBy('d.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 查找大于指定大小的磁盘
     *
     * @param int $minSizeInGb 最小大小（GB）
     * @return Disk[]
     */
    public function findLargerThan(int $minSizeInGb): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.sizeInGb >= :minSize')
            ->setParameter('minSize', $minSizeInGb)
            ->orderBy('d.sizeInGb', 'DESC')
            ->getQuery()
            ->getResult();
    }
} 