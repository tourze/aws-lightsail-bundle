<?php

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Entity\AwsCredential;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AwsCredential>
 *
 * @method AwsCredential|null find($id, $lockMode = null, $lockVersion = null)
 * @method AwsCredential|null findOneBy(array $criteria, array $orderBy = null)
 * @method AwsCredential[]    findAll()
 * @method AwsCredential[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AwsCredentialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AwsCredential::class);
    }

    /**
     * 查找默认凭证
     *
     * @return AwsCredential|null
     */
    public function findDefault(): ?AwsCredential
    {
        return $this->findOneBy(['isDefault' => true]);
    }

    /**
     * 按区域查找凭证
     *
     * @param string $region 区域
     * @return AwsCredential[]
     */
    public function findByRegion(string $region): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.region = :region')
            ->setParameter('region', $region)
            ->orderBy('a.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
} 