<?php

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Entity\Distribution;
use AwsLightsailBundle\Enum\DistributionStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Distribution>
 *
 * @method Distribution|null find($id, $lockMode = null, $lockVersion = null)
 * @method Distribution|null findOneBy(array $criteria, array $orderBy = null)
 * @method Distribution[]    findAll()
 * @method Distribution[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DistributionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Distribution::class);
    }

    /**
     * 按状态查找分发
     *
     * @param DistributionStatusEnum $status 分发状态
     * @return Distribution[]
     */
    public function findByStatus(DistributionStatusEnum $status): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.status = :status')
            ->setParameter('status', $status)
            ->orderBy('d.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 按区域查找分发
     *
     * @param string $region 区域
     * @return Distribution[]
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
     * 查找已启用的分发
     *
     * @return Distribution[]
     */
    public function findEnabled(): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.isEnabled = :isEnabled')
            ->setParameter('isEnabled', true)
            ->orderBy('d.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 按证书名称查找分发
     *
     * @param string $certificateName 证书名称
     * @return Distribution[]
     */
    public function findByCertificate(string $certificateName): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.certificateName = :certificateName')
            ->setParameter('certificateName', $certificateName)
            ->orderBy('d.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 查找包含特定域名的分发
     *
     * @param string $domainName 域名
     * @return Distribution[]
     */
    public function findByDomainName(string $domainName): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.defaultDomainName = :domainName OR d.alternativeDomainNames LIKE :pattern')
            ->setParameter('domainName', $domainName)
            ->setParameter('pattern', '%' . $domainName . '%')
            ->orderBy('d.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
} 