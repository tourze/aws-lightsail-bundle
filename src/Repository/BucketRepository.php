<?php

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Entity\Bucket;
use AwsLightsailBundle\Enum\BucketAccessRuleEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Bucket>
 *
 * @method Bucket|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bucket|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bucket[]    findAll()
 * @method Bucket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BucketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bucket::class);
    }

    /**
     * 按区域查找存储桶
     *
     * @param string $region 区域
     * @return Bucket[]
     */
    public function findByRegion(string $region): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.region = :region')
            ->setParameter('region', $region)
            ->orderBy('b.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 按访问规则查找存储桶
     *
     * @param BucketAccessRuleEnum $accessRule 访问规则
     * @return Bucket[]
     */
    public function findByAccessRule(BucketAccessRuleEnum $accessRule): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.accessRules = :accessRule')
            ->setParameter('accessRule', $accessRule)
            ->orderBy('b.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 获取指定大小以上的存储桶
     *
     * @param int $sizeInMb 最小大小（MB）
     * @return Bucket[]
     */
    public function findLargerThan(int $sizeInMb): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.sizeInMb >= :size')
            ->setParameter('size', $sizeInMb)
            ->orderBy('b.sizeInMb', 'DESC')
            ->getQuery()
            ->getResult();
    }
} 