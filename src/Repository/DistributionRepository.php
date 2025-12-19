<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Entity\Distribution;
use AwsLightsailBundle\Enum\DistributionStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<Distribution>
 */
#[AsRepository(entityClass: Distribution::class)]
final class DistributionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Distribution::class);
    }

    /**
     * 按状态查找分发
     *
     * @param DistributionStatusEnum $status 分发状态
     *
     * @return Distribution[]
     * @phpstan-return array<int, Distribution>
     */
    public function findByStatus(DistributionStatusEnum $status): array
    {
        /** @var array<int, Distribution> $result */
        $result = $this->createQueryBuilder('d')
            ->andWhere('d.status = :status')
            ->setParameter('status', $status)
            ->orderBy('d.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 按区域查找分发
     *
     * @param string $region 区域
     *
     * @return Distribution[]
     * @phpstan-return array<int, Distribution>
     */
    public function findByRegion(string $region): array
    {
        /** @var array<int, Distribution> $result */
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
     * 查找已启用的分发
     *
     * @return Distribution[]
     * @phpstan-return array<int, Distribution>
     */
    public function findEnabled(): array
    {
        /** @var array<int, Distribution> $result */
        $result = $this->createQueryBuilder('d')
            ->andWhere('d.isEnabled = :isEnabled')
            ->setParameter('isEnabled', true)
            ->orderBy('d.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 按证书名称查找分发
     *
     * @param string $certificateName 证书名称
     *
     * @return Distribution[]
     * @phpstan-return array<int, Distribution>
     */
    public function findByCertificate(string $certificateName): array
    {
        /** @var array<int, Distribution> $result */
        $result = $this->createQueryBuilder('d')
            ->andWhere('d.certificateName = :certificateName')
            ->setParameter('certificateName', $certificateName)
            ->orderBy('d.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 查找包含特定域名的分发
     *
     * @param string $domainName 域名
     *
     * @return Distribution[]
     * @phpstan-return array<int, Distribution>
     */
    public function findByDomainName(string $domainName): array
    {
        /** @var array<int, Distribution> $result */
        $result = $this->createQueryBuilder('d')
            ->andWhere('d.defaultDomainName = :domainName OR d.alternativeDomainNames LIKE :pattern')
            ->setParameter('domainName', $domainName)
            ->setParameter('pattern', '%' . $domainName . '%')
            ->orderBy('d.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    public function save(Distribution $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Distribution $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
