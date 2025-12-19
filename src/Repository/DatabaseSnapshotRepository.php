<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Entity\Database;
use AwsLightsailBundle\Entity\DatabaseSnapshot;
use AwsLightsailBundle\Enum\DatabaseEngineEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<DatabaseSnapshot>
 */
#[AsRepository(entityClass: DatabaseSnapshot::class)]
final class DatabaseSnapshotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DatabaseSnapshot::class);
    }

    /**
     * 按数据库查找快照
     *
     * @param Database $database 数据库
     *
     * @return DatabaseSnapshot[]
     * @phpstan-return array<int, DatabaseSnapshot>
     */
    public function findByDatabase(Database $database): array
    {
        /** @var array<int, DatabaseSnapshot> $result */
        $result = $this->createQueryBuilder('ds')
            ->andWhere('ds.database = :database')
            ->setParameter('database', $database)
            ->orderBy('ds.createTime', 'DESC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 按数据库名称查找快照
     *
     * @param string $databaseName 数据库名称
     *
     * @return DatabaseSnapshot[]
     * @phpstan-return array<int, DatabaseSnapshot>
     */
    public function findByDatabaseName(string $databaseName): array
    {
        /** @var array<int, DatabaseSnapshot> $result */
        $result = $this->createQueryBuilder('ds')
            ->andWhere('ds.databaseName = :databaseName')
            ->setParameter('databaseName', $databaseName)
            ->orderBy('ds.createTime', 'DESC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 按数据库引擎查找快照
     *
     * @param DatabaseEngineEnum $engine 数据库引擎
     *
     * @return DatabaseSnapshot[]
     * @phpstan-return array<int, DatabaseSnapshot>
     */
    public function findByEngine(DatabaseEngineEnum $engine): array
    {
        /** @var array<int, DatabaseSnapshot> $result */
        $result = $this->createQueryBuilder('ds')
            ->andWhere('ds.engine = :engine')
            ->setParameter('engine', $engine)
            ->orderBy('ds.createTime', 'DESC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 按区域查找数据库快照
     *
     * @param string $region 区域
     *
     * @return DatabaseSnapshot[]
     * @phpstan-return array<int, DatabaseSnapshot>
     */
    public function findByRegion(string $region): array
    {
        /** @var array<int, DatabaseSnapshot> $result */
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
     * @return DatabaseSnapshot[]
     * @phpstan-return array<int, DatabaseSnapshot>
     */
    public function findAutoSnapshots(): array
    {
        /** @var array<int, DatabaseSnapshot> $result */
        $result = $this->createQueryBuilder('ds')
            ->andWhere('ds.isFromAutoSnapshot = :isAuto')
            ->setParameter('isAuto', true)
            ->orderBy('ds.createTime', 'DESC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    public function save(DatabaseSnapshot $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DatabaseSnapshot $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
