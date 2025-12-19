<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Entity\Bucket;
use AwsLightsailBundle\Enum\BucketAccessRuleEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<Bucket>
 */
#[AsRepository(entityClass: Bucket::class)]
final class BucketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bucket::class);
    }

    /**
     * 按区域查找存储桶
     *
     * @param string $region 区域
     *
     * @return Bucket[]
     * @phpstan-return array<int, Bucket>
     */
    public function findByRegion(string $region): array
    {
        /** @var array<int, Bucket> $result */
        $result = $this->createQueryBuilder('b')
            ->andWhere('b.region = :region')
            ->setParameter('region', $region)
            ->orderBy('b.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        return $result;
    }

    /**
     * 按访问规则查找存储桶
     *
     * @param BucketAccessRuleEnum $accessRule 访问规则
     *
     * @return Bucket[]
     * @phpstan-return array<int, Bucket>
     */
    public function findByAccessRule(BucketAccessRuleEnum $accessRule): array
    {
        /** @var array<int, Bucket> $result */
        $result = $this->createQueryBuilder('b')
            ->andWhere('b.accessRules = :accessRule')
            ->setParameter('accessRule', $accessRule)
            ->orderBy('b.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        return $result;
    }

    /**
     * 获取指定大小以上的存储桶
     *
     * @param int $sizeInMb 最小大小（MB）
     *
     * @return Bucket[]
     * @phpstan-return array<int, Bucket>
     */
    public function findLargerThan(int $sizeInMb): array
    {
        /** @var array<int, Bucket> $result */
        $result = $this->createQueryBuilder('b')
            ->andWhere('b.sizeInMb >= :size')
            ->setParameter('size', $sizeInMb)
            ->orderBy('b.sizeInMb', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        return $result;
    }

    public function save(Bucket $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Bucket $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
