<?php

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Entity\ContactMethod;
use AwsLightsailBundle\Enum\ContactMethodStatusEnum;
use AwsLightsailBundle\Enum\ContactMethodTypeEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ContactMethod>
 *
 * @method ContactMethod|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactMethod|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactMethod[]    findAll()
 * @method ContactMethod[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactMethodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactMethod::class);
    }

    /**
     * 按联系方式类型查找
     *
     * @param ContactMethodTypeEnum $type 联系方式类型
     * @return ContactMethod[]
     */
    public function findByType(ContactMethodTypeEnum $type): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.type = :type')
            ->setParameter('type', $type)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 按状态查找联系方式
     *
     * @param ContactMethodStatusEnum $status 状态
     * @return ContactMethod[]
     */
    public function findByStatus(ContactMethodStatusEnum $status): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.status = :status')
            ->setParameter('status', $status)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 按联系端点查找
     *
     * @param string $contactEndpoint 联系端点（如邮箱地址或手机号）
     * @return ContactMethod|null
     */
    public function findByContactEndpoint(string $contactEndpoint): ?ContactMethod
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.contactEndpoint = :contactEndpoint')
            ->setParameter('contactEndpoint', $contactEndpoint)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
