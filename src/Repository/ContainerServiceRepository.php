<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Entity\ContainerService;
use AwsLightsailBundle\Enum\ContainerServicePowerEnum;
use AwsLightsailBundle\Enum\ContainerServiceStateEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<ContainerService>
 */
#[AsRepository(entityClass: ContainerService::class)]
class ContainerServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContainerService::class);
    }

    /**
     * 按状态查找容器服务
     *
     * @param ContainerServiceStateEnum $state 状态
     *
     * @return ContainerService[]
     * @phpstan-return array<int, ContainerService>
     */
    public function findByState(ContainerServiceStateEnum $state): array
    {
        /** @var array<int, ContainerService> $result */
        $result = $this->createQueryBuilder('c')
            ->andWhere('c.state = :state')
            ->setParameter('state', $state)
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 按容器性能等级查找
     *
     * @param ContainerServicePowerEnum $power 性能等级
     *
     * @return ContainerService[]
     * @phpstan-return array<int, ContainerService>
     */
    public function findByPower(ContainerServicePowerEnum $power): array
    {
        /** @var array<int, ContainerService> $result */
        $result = $this->createQueryBuilder('c')
            ->andWhere('c.power = :power')
            ->setParameter('power', $power)
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 按区域查找容器服务
     *
     * @param string $region 区域
     *
     * @return ContainerService[]
     * @phpstan-return array<int, ContainerService>
     */
    public function findByRegion(string $region): array
    {
        /** @var array<int, ContainerService> $result */
        $result = $this->createQueryBuilder('c')
            ->andWhere('c.region = :region')
            ->setParameter('region', $region)
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 查找至少有指定数量副本的容器服务
     *
     * @param int $minScale 最小副本数
     *
     * @return ContainerService[]
     * @phpstan-return array<int, ContainerService>
     */
    public function findByMinimumScale(int $minScale): array
    {
        /** @var array<int, ContainerService> $result */
        $result = $this->createQueryBuilder('c')
            ->andWhere('c.scale >= :minScale')
            ->setParameter('minScale', $minScale)
            ->orderBy('c.scale', 'DESC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    public function save(ContainerService $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ContainerService $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
