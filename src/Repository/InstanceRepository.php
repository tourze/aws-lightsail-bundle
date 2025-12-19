<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Instance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<Instance>
 */
#[AsRepository(entityClass: Instance::class)]
final class InstanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Instance::class);
    }

    public function findOneByNameAndCredential(string $name, AwsCredential $credential): ?Instance
    {
        return $this->findOneBy(['name' => $name, 'credential' => $credential]);
    }

    /**
     * 按实例状态查找
     *
     * @param string $state 实例状态
     *
     * @return Instance[]
     * @phpstan-return array<int, Instance>
     */
    public function findByState(string $state): array
    {
        /** @var array<int, Instance> $result */
        $result = $this->createQueryBuilder('i')
            ->andWhere('i.state = :state')
            ->setParameter('state', $state)
            ->orderBy('i.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 按区域查找
     *
     * @param string $region 区域
     *
     * @return Instance[]
     * @phpstan-return array<int, Instance>
     */
    public function findByRegion(string $region): array
    {
        /** @var array<int, Instance> $result */
        $result = $this->createQueryBuilder('i')
            ->andWhere('i.region = :region')
            ->setParameter('region', $region)
            ->orderBy('i.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 按蓝图类型查找
     *
     * @param string $blueprint 蓝图类型
     *
     * @return Instance[]
     * @phpstan-return array<int, Instance>
     */
    public function findByBlueprint(string $blueprint): array
    {
        /** @var array<int, Instance> $result */
        $result = $this->createQueryBuilder('i')
            ->andWhere('i.blueprint = :blueprint')
            ->setParameter('blueprint', $blueprint)
            ->orderBy('i.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    public function save(Instance $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Instance $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
