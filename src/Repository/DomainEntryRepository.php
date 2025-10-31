<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Entity\Domain;
use AwsLightsailBundle\Entity\DomainEntry;
use AwsLightsailBundle\Enum\DnsRecordTypeEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<DomainEntry>
 */
#[AsRepository(entityClass: DomainEntry::class)]
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
     *
     * @return DomainEntry[]
     * @phpstan-return array<int, DomainEntry>
     */
    public function findByDomain(Domain $domain): array
    {
        /** @var array<int, DomainEntry> $result */
        $result = $this->createQueryBuilder('de')
            ->andWhere('de.domain = :domain')
            ->setParameter('domain', $domain)
            ->orderBy('de.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 按记录类型查找域名记录
     *
     * @param DnsRecordTypeEnum $type 记录类型
     *
     * @return DomainEntry[]
     * @phpstan-return array<int, DomainEntry>
     */
    public function findByType(DnsRecordTypeEnum $type): array
    {
        /** @var array<int, DomainEntry> $result */
        $result = $this->createQueryBuilder('de')
            ->andWhere('de.type = :type')
            ->setParameter('type', $type)
            ->orderBy('de.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 按记录名称查找域名记录
     *
     * @param string $name 记录名称
     *
     * @return DomainEntry[]
     * @phpstan-return array<int, DomainEntry>
     */
    public function findByName(string $name): array
    {
        /** @var array<int, DomainEntry> $result */
        $result = $this->createQueryBuilder('de')
            ->andWhere('de.name = :name')
            ->setParameter('name', $name)
            ->orderBy('de.type', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 按记录值查找域名记录
     *
     * @param string $value 记录值
     *
     * @return DomainEntry[]
     * @phpstan-return array<int, DomainEntry>
     */
    public function findByValue(string $value): array
    {
        /** @var array<int, DomainEntry> $result */
        $result = $this->createQueryBuilder('de')
            ->andWhere('de.value = :value')
            ->setParameter('value', $value)
            ->orderBy('de.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 查找别名记录
     *
     * @return DomainEntry[]
     * @phpstan-return array<int, DomainEntry>
     */
    public function findAliasRecords(): array
    {
        /** @var array<int, DomainEntry> $result */
        $result = $this->createQueryBuilder('de')
            ->andWhere('de.isAlias = :isAlias')
            ->setParameter('isAlias', true)
            ->orderBy('de.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    public function save(DomainEntry $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DomainEntry $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
