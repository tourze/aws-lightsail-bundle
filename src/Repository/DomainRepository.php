<?php

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Entity\Domain;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Domain>
 *
 * @method Domain|null find($id, $lockMode = null, $lockVersion = null)
 * @method Domain|null findOneBy(array $criteria, array $orderBy = null)
 * @method Domain[]    findAll()
 * @method Domain[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DomainRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Domain::class);
    }

    /**
     * 按区域查找域名
     *
     * @param string $region 区域
     * @return Domain[]
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
     * 查找托管的域名
     *
     * @return Domain[]
     */
    public function findManaged(): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.isManaged = :isManaged')
            ->setParameter('isManaged', true)
            ->orderBy('d.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 按名称模式查找域名
     *
     * @param string $pattern 名称模式
     * @return Domain[]
     */
    public function findByNamePattern(string $pattern): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.name LIKE :pattern')
            ->setParameter('pattern', '%' . $pattern . '%')
            ->orderBy('d.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
} 