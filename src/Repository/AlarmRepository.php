<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Entity\Alarm;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<Alarm>
 */
#[AsRepository(entityClass: Alarm::class)]
final class AlarmRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Alarm::class);
    }

    /**
     * 按资源名称查找告警
     *
     * @param string $resourceName 资源名称
     *
     * @return Alarm[]
     * @phpstan-return array<int, Alarm>
     */
    public function findByResourceName(string $resourceName): array
    {
        /** @var array<int, Alarm> $result */
        $result = $this->createQueryBuilder('a')
            ->andWhere('a.resourceName = :resourceName')
            ->setParameter('resourceName', $resourceName)
            ->getQuery()
            ->getResult()
        ;

        return $result;
    }

    /**
     * 按资源类型查找告警
     *
     * @param string $resourceType 资源类型
     *
     * @return Alarm[]
     * @phpstan-return array<int, Alarm>
     */
    public function findByResourceType(string $resourceType): array
    {
        /** @var array<int, Alarm> $result */
        $result = $this->createQueryBuilder('a')
            ->andWhere('a.resourceType = :resourceType')
            ->setParameter('resourceType', $resourceType)
            ->getQuery()
            ->getResult()
        ;

        return $result;
    }

    /**
     * 按状态查找告警
     *
     * @param string $state 告警状态
     *
     * @return Alarm[]
     * @phpstan-return array<int, Alarm>
     */
    public function findByState(string $state): array
    {
        /** @var array<int, Alarm> $result */
        $result = $this->createQueryBuilder('a')
            ->andWhere('a.state = :state')
            ->setParameter('state', $state)
            ->getQuery()
            ->getResult()
        ;

        return $result;
    }

    public function save(Alarm $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Alarm $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
