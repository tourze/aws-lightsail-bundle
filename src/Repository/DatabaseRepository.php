<?php

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Entity\Database;
use AwsLightsailBundle\Enum\DatabaseEngineEnum;
use AwsLightsailBundle\Enum\DatabaseStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Database>
 *
 * @method Database|null find($id, $lockMode = null, $lockVersion = null)
 * @method Database|null findOneBy(array $criteria, array $orderBy = null)
 * @method Database[]    findAll()
 * @method Database[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
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
     * @return Database[]
     */
    public function findByEngine(DatabaseEngineEnum $engine): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.engine = :engine')
            ->setParameter('engine', $engine)
            ->orderBy('d.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 按数据库状态查找
     *
     * @param DatabaseStatusEnum $status 数据库状态
     * @return Database[]
     */
    public function findByStatus(DatabaseStatusEnum $status): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.status = :status')
            ->setParameter('status', $status)
            ->orderBy('d.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 按区域查找数据库
     *
     * @param string $region 区域
     * @return Database[]
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
     * 查找公开访问的数据库
     *
     * @return Database[]
     */
    public function findPubliclyAccessible(): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.publiclyAccessible = :publiclyAccessible')
            ->setParameter('publiclyAccessible', true)
            ->orderBy('d.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
} 