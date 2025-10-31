<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Repository;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\KeyPair;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<KeyPair>
 */
#[AsRepository(entityClass: KeyPair::class)]
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
     *
     * @return KeyPair[]
     * @phpstan-return array<int, KeyPair>
     */
    public function findByRegion(string $region): array
    {
        /** @var array<int, KeyPair> $result */
        $result = $this->createQueryBuilder('kp')
            ->andWhere('kp.region = :region')
            ->setParameter('region', $region)
            ->orderBy('kp.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 查找默认密钥对
     */
    public function findDefault(): ?KeyPair
    {
        return $this->findOneBy(['isDefault' => true]);
    }

    /**
     * 按指纹查找密钥对
     *
     * @param string $fingerprint 指纹
     */
    public function findByFingerprint(string $fingerprint): ?KeyPair
    {
        return $this->findOneBy(['fingerprint' => $fingerprint]);
    }

    /**
     * 按标签查找密钥对
     *
     * @param string $tagName  标签名称
     * @param string $tagValue 标签值
     *
     * @return KeyPair[]
     * @phpstan-return array<int, KeyPair>
     */
    public function findByTag(string $tagName, string $tagValue): array
    {
        $qb = $this->createQueryBuilder('kp');

        /** @var array<int, KeyPair> $result */
        $result = $qb->andWhere($qb->expr()->like('kp.tags', ':tagPattern'))
            ->setParameter('tagPattern', '%"' . $tagName . '":"' . $tagValue . '"%')
            ->orderBy('kp.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $result;
    }

    /**
     * 根据名称、凭证和区域查找密钥对
     *
     * @param string        $name       密钥对名称
     * @param AwsCredential $credential AWS 凭证
     * @param string        $region     区域
     */
    public function findOneByNameAndCredentialAndRegion(string $name, AwsCredential $credential, string $region): ?KeyPair
    {
        /** @var KeyPair|null $result */
        $result = $this->createQueryBuilder('kp')
            ->andWhere('kp.name = :name')
            ->andWhere('kp.credential = :credential')
            ->andWhere('kp.region = :region')
            ->setParameter('name', $name)
            ->setParameter('credential', $credential)
            ->setParameter('region', $region)
            ->getQuery()
            ->getOneOrNullResult()
        ;
        return $result;
    }

    public function save(KeyPair $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(KeyPair $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
