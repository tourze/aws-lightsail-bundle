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
}
