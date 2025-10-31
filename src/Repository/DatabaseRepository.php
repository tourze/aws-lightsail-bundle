<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Entity\Database;
use AwsLightsailBundle\Enum\DatabaseEngineEnum;
use AwsLightsailBundle\Enum\DatabaseStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<Database>
 */
#[AsRepository(entityClass: Database::class)]
class DatabaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Database::class);
    }

    /**
     * 按数据库引擎类型查找
     *
     * @param DatabaseEngineEnum $engine 数据库引擎
     *
     * @return Database[]
     * @phpstan-return array<int, Database>
     */
    public function findByEngine(DatabaseEngineEnum $engine): array
    {
        /** @var array<int, Database> $result */
        $result = $this->createQueryBuilder('d')
            ->andWhere('d.engine = :engine')
            ->setParameter('engine', $engine)
            ->orderBy('d.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 按数据库状态查找
     *
     * @param DatabaseStatusEnum $status 数据库状态
     *
     * @return Database[]
     * @phpstan-return array<int, Database>
     */
    public function findByStatus(DatabaseStatusEnum $status): array
    {
        /** @var array<int, Database> $result */
        $result = $this->createQueryBuilder('d')
            ->andWhere('d.status = :status')
            ->setParameter('status', $status)
            ->orderBy('d.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 按区域查找数据库
     *
     * @param string $region 区域
     *
     * @return Database[]
     * @phpstan-return array<int, Database>
     */
    public function findByRegion(string $region): array
    {
        /** @var array<int, Database> $result */
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
     * 查找公开访问的数据库
     *
     * @return Database[]
     * @phpstan-return array<int, Database>
     */
    public function findPubliclyAccessible(): array
    {
        /** @var array<int, Database> $result */
        $result = $this->createQueryBuilder('d')
            ->andWhere('d.publiclyAccessible = :publiclyAccessible')
            ->setParameter('publiclyAccessible', true)
            ->orderBy('d.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    public function save(Database $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Database $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
