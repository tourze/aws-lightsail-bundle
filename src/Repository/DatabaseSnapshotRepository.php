<?php

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Entity\Database;
use AwsLightsailBundle\Entity\DatabaseSnapshot;
use AwsLightsailBundle\Enum\DatabaseEngineEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DatabaseSnapshot>
 *
 * @method DatabaseSnapshot|null find($id, $lockMode = null, $lockVersion = null)
 * @method DatabaseSnapshot|null findOneBy(array $criteria, array $orderBy = null)
 * @method DatabaseSnapshot[]    findAll()
 * @method DatabaseSnapshot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DatabaseSnapshotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DatabaseSnapshot::class);
    }

    /**
     * 按数据库查找快照
     *
     * @param Database $database 数据库
     * @return DatabaseSnapshot[]
     */
    public function findByDatabase(Database $database): array
    {
        return $this->createQueryBuilder('ds')
            ->andWhere('ds.database = :database')
            ->setParameter('database', $database)
            ->orderBy('ds.createTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 按数据库名称查找快照
     *
     * @param string $databaseName 数据库名称
     * @return DatabaseSnapshot[]
     */
    public function findByDatabaseName(string $databaseName): array
    {
        return $this->createQueryBuilder('ds')
            ->andWhere('ds.databaseName = :databaseName')
            ->setParameter('databaseName', $databaseName)
            ->orderBy('ds.createTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 按数据库引擎查找快照
     *
     * @param DatabaseEngineEnum $engine 数据库引擎
     * @return DatabaseSnapshot[]
     */
    public function findByEngine(DatabaseEngineEnum $engine): array
    {
        return $this->createQueryBuilder('ds')
            ->andWhere('ds.engine = :engine')
            ->setParameter('engine', $engine)
            ->orderBy('ds.createTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 按区域查找数据库快照
     *
     * @param string $region 区域
     * @return DatabaseSnapshot[]
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
     * @return DatabaseSnapshot[]
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
} 