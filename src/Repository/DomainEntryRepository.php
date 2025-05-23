<?php

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Entity\Domain;
use AwsLightsailBundle\Entity\DomainEntry;
use AwsLightsailBundle\Enum\DnsRecordTypeEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DomainEntry>
 *
 * @method DomainEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method DomainEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method DomainEntry[]    findAll()
 * @method DomainEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DomainEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DomainEntry::class);
    }

    /**
     * 按域名查找域名记录
     *
     * @param Domain $domain 域名
     * @return DomainEntry[]
     */
    public function findByDomain(Domain $domain): array
    {
        return $this->createQueryBuilder('de')
            ->andWhere('de.domain = :domain')
            ->setParameter('domain', $domain)
            ->orderBy('de.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 按记录类型查找域名记录
     *
     * @param DnsRecordTypeEnum $type 记录类型
     * @return DomainEntry[]
     */
    public function findByType(DnsRecordTypeEnum $type): array
    {
        return $this->createQueryBuilder('de')
            ->andWhere('de.type = :type')
            ->setParameter('type', $type)
            ->orderBy('de.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 按记录名称查找域名记录
     *
     * @param string $name 记录名称
     * @return DomainEntry[]
     */
    public function findByName(string $name): array
    {
        return $this->createQueryBuilder('de')
            ->andWhere('de.name = :name')
            ->setParameter('name', $name)
            ->orderBy('de.type', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 按记录值查找域名记录
     *
     * @param string $value 记录值
     * @return DomainEntry[]
     */
    public function findByValue(string $value): array
    {
        return $this->createQueryBuilder('de')
            ->andWhere('de.value = :value')
            ->setParameter('value', $value)
            ->orderBy('de.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 查找别名记录
     *
     * @return DomainEntry[]
     */
    public function findAliasRecords(): array
    {
        return $this->createQueryBuilder('de')
            ->andWhere('de.isAlias = :isAlias')
            ->setParameter('isAlias', true)
            ->orderBy('de.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
