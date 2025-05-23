<?php

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Entity\Certificate;
use AwsLightsailBundle\Enum\CertificateStatusEnum;
use Carbon\Carbon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Certificate>
 *
 * @method Certificate|null find($id, $lockMode = null, $lockVersion = null)
 * @method Certificate|null findOneBy(array $criteria, array $orderBy = null)
 * @method Certificate[]    findAll()
 * @method Certificate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
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
     * @return Certificate[]
     */
    public function findByDomainName(string $domainName): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.domainName = :domainName')
            ->setParameter('domainName', $domainName)
            ->orderBy('c.createTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 按状态查找证书
     *
     * @param CertificateStatusEnum $status 证书状态
     * @return Certificate[]
     */
    public function findByStatus(CertificateStatusEnum $status): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.status = :status')
            ->setParameter('status', $status)
            ->orderBy('c.createTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 查找即将过期的证书
     *
     * @param int $daysThreshold 天数阈值
     * @return Certificate[]
     */
    public function findExpiringCertificates(int $daysThreshold = 30): array
    {
        $expiryDate = new \DateTimeImmutable("+" . $daysThreshold . " days");

        return $this->createQueryBuilder('c')
            ->andWhere('c.notAfter <= :expiryDate')
            ->andWhere('c.notAfter > :now')
            ->setParameter('expiryDate', $expiryDate)
            ->setParameter('now', Carbon::now())
            ->orderBy('c.notAfter', 'ASC')
            ->getQuery()
            ->getResult();
    }
} 