<?php

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\KeyPair;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<KeyPair>
 *
 * @method KeyPair|null find($id, $lockMode = null, $lockVersion = null)
 * @method KeyPair|null findOneBy(array $criteria, array $orderBy = null)
 * @method KeyPair[]    findAll()
 * @method KeyPair[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class KeyPairRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, KeyPair::class);
    }

    /**
     * 按区域查找密钥对
     *
     * @param string $region 区域
     * @return KeyPair[]
     */
    public function findByRegion(string $region): array
    {
        return $this->createQueryBuilder('kp')
            ->andWhere('kp.region = :region')
            ->setParameter('region', $region)
            ->orderBy('kp.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 查找默认密钥对
     *
     * @return KeyPair|null
     */
    public function findDefault(): ?KeyPair
    {
        return $this->findOneBy(['isDefault' => true]);
    }

    /**
     * 按指纹查找密钥对
     *
     * @param string $fingerprint 指纹
     * @return KeyPair|null
     */
    public function findByFingerprint(string $fingerprint): ?KeyPair
    {
        return $this->findOneBy(['fingerprint' => $fingerprint]);
    }

    /**
     * 按标签查找密钥对
     *
     * @param string $tagName 标签名称
     * @param string $tagValue 标签值
     * @return KeyPair[]
     */
    public function findByTag(string $tagName, string $tagValue): array
    {
        $qb = $this->createQueryBuilder('kp');
        return $qb->andWhere($qb->expr()->like('kp.tags', ':tagPattern'))
            ->setParameter('tagPattern', '%"' . $tagName . '":"' . $tagValue . '"%')
            ->orderBy('kp.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 根据名称、凭证和区域查找密钥对
     *
     * @param string $name 密钥对名称
     * @param AwsCredential $credential AWS 凭证
     * @param string $region 区域
     * @return KeyPair|null
     */
    public function findOneByNameAndCredentialAndRegion(string $name, AwsCredential $credential, string $region): ?KeyPair
    {
        return $this->createQueryBuilder('kp')
            ->andWhere('kp.name = :name')
            ->andWhere('kp.credential = :credential')
            ->andWhere('kp.region = :region')
            ->setParameter('name', $name)
            ->setParameter('credential', $credential)
            ->setParameter('region', $region)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
