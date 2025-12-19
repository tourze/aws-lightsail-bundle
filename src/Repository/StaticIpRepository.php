<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Entity\Instance;
use AwsLightsailBundle\Entity\StaticIp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<StaticIp>
 */
#[AsRepository(entityClass: StaticIp::class)]
final class StaticIpRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StaticIp::class);
    }

    /**
     * 按区域查找静态IP
     *
     * @param string $region 区域
     *
     * @return StaticIp[]
     * @phpstan-return array<int, StaticIp>
     */
    public function findByRegion(string $region): array
    {
        /** @var array<int, StaticIp> $result */
        $result = $this->createQueryBuilder('s')
            ->andWhere('s.region = :region')
            ->setParameter('region', $region)
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 查找已分配给实例的静态IP
     *
     * @return StaticIp[]
     * @phpstan-return array<int, StaticIp>
     */
    public function findAttached(): array
    {
        /** @var array<int, StaticIp> $result */
        $result = $this->createQueryBuilder('s')
            ->andWhere('s.attachedTo IS NOT NULL')
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 查找未分配的静态IP
     *
     * @return StaticIp[]
     * @phpstan-return array<int, StaticIp>
     */
    public function findDetached(): array
    {
        /** @var array<int, StaticIp> $result */
        $result = $this->createQueryBuilder('s')
            ->andWhere('s.attachedTo IS NULL')
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 按实例查找静态IP
     *
     * @param Instance $instance 实例
     */
    public function findByInstance(Instance $instance): ?StaticIp
    {
        /** @var StaticIp|null $result */
        $result = $this->createQueryBuilder('s')
            ->andWhere('s.attachedTo = :instanceName')
            ->setParameter('instanceName', $instance->getName())
            ->getQuery()
            ->getOneOrNullResult()
        ;
        return $result;
    }

    /**
     * 按IP地址查找静态IP
     *
     * @param string $ipAddress IP地址
     */
    public function findByIpAddress(string $ipAddress): ?StaticIp
    {
        return $this->findOneBy(['ipAddress' => $ipAddress]);
    }

    public function save(StaticIp $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(StaticIp $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
