<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Entity\Domain;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<Domain>
 */
#[AsRepository(entityClass: Domain::class)]
final class DomainRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Domain::class);
    }

    /**
     * 按区域查找域名
     *
     * @param string $region 区域
     *
     * @return Domain[]
     * @phpstan-return array<int, Domain>
     */
    public function findByRegion(string $region): array
    {
        /** @var array<int, Domain> $result */
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
     * 查找托管的域名
     *
     * @return Domain[]
     * @phpstan-return array<int, Domain>
     */
    public function findManaged(): array
    {
        /** @var array<int, Domain> $result */
        $result = $this->createQueryBuilder('d')
            ->andWhere('d.isManaged = :isManaged')
            ->setParameter('isManaged', true)
            ->orderBy('d.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 按名称模式查找域名
     *
     * @param string $pattern 名称模式
     *
     * @return Domain[]
     * @phpstan-return array<int, Domain>
     */
    public function findByNamePattern(string $pattern): array
    {
        /** @var array<int, Domain> $result */
        $result = $this->createQueryBuilder('d')
            ->andWhere('d.name LIKE :pattern')
            ->setParameter('pattern', '%' . $pattern . '%')
            ->orderBy('d.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    public function save(Domain $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Domain $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
