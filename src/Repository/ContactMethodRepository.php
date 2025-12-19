<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Entity\ContactMethod;
use AwsLightsailBundle\Enum\ContactMethodStatusEnum;
use AwsLightsailBundle\Enum\ContactMethodTypeEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<ContactMethod>
 */
#[AsRepository(entityClass: ContactMethod::class)]
final class ContactMethodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactMethod::class);
    }

    /**
     * 按联系方式类型查找
     *
     * @param ContactMethodTypeEnum $type 联系方式类型
     *
     * @return ContactMethod[]
     * @phpstan-return array<int, ContactMethod>
     */
    public function findByType(ContactMethodTypeEnum $type): array
    {
        /** @var array<int, ContactMethod> $result */
        $result = $this->createQueryBuilder('c')
            ->andWhere('c.type = :type')
            ->setParameter('type', $type)
            ->orderBy('c.createTime', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        return $result;
    }

    /**
     * 按状态查找联系方式
     *
     * @param ContactMethodStatusEnum $status 状态
     *
     * @return ContactMethod[]
     * @phpstan-return array<int, ContactMethod>
     */
    public function findByStatus(ContactMethodStatusEnum $status): array
    {
        /** @var array<int, ContactMethod> $result */
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
     * 按联系端点查找
     *
     * @param string $contactEndpoint 联系端点（如邮箱地址或手机号）
     */
    public function findByContactEndpoint(string $contactEndpoint): ?ContactMethod
    {
        /** @var ContactMethod|null $result */
        $result = $this->createQueryBuilder('c')
            ->andWhere('c.contactEndpoint = :contactEndpoint')
            ->setParameter('contactEndpoint', $contactEndpoint)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $result;
    }

    public function save(ContactMethod $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ContactMethod $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
