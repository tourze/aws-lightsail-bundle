<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Entity\Certificate;
use AwsLightsailBundle\Enum\CertificateStatusEnum;
use Carbon\CarbonImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<Certificate>
 */
#[AsRepository(entityClass: Certificate::class)]
class CertificateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Certificate::class);
    }

    /**
     * 按域名查找证书
     *
     * @param string $domainName 域名
     *
     * @return Certificate[]
     * @phpstan-return array<int, Certificate>
     */
    public function findByDomainName(string $domainName): array
    {
        /** @var array<int, Certificate> $result */
        $result = $this->createQueryBuilder('c')
            ->andWhere('c.domainName = :domainName')
            ->setParameter('domainName', $domainName)
            ->orderBy('c.createTime', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        return $result;
    }

    /**
     * 按状态查找证书
     *
     * @param CertificateStatusEnum $status 证书状态
     *
     * @return Certificate[]
     * @phpstan-return array<int, Certificate>
     */
    public function findByStatus(CertificateStatusEnum $status): array
    {
        /** @var array<int, Certificate> $result */
        $result = $this->createQueryBuilder('c')
            ->andWhere('c.status = :status')
            ->setParameter('status', $status)
            ->orderBy('c.createTime', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        return $result;
    }

    /**
     * 查找即将过期的证书
     *
     * @param int $daysThreshold 天数阈值
     *
     * @return Certificate[]
     * @phpstan-return array<int, Certificate>
     */
    public function findExpiringCertificates(int $daysThreshold = 30): array
    {
        $expiryDate = new \DateTimeImmutable('+' . $daysThreshold . ' days');

        /** @var array<int, Certificate> $result */
        $result = $this->createQueryBuilder('c')
            ->andWhere('c.validToTime <= :expiryDate')
            ->andWhere('c.validToTime > :now')
            ->setParameter('expiryDate', $expiryDate)
            ->setParameter('now', CarbonImmutable::now())
            ->orderBy('c.validToTime', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        return $result;
    }

    public function save(Certificate $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Certificate $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
