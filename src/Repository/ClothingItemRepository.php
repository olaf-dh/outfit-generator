<?php

namespace App\Repository;

use App\ClothingItem\Enum\BodyZone;
use App\ClothingItem\Enum\ClothingItemStatus;
use App\Entity\ClothingItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<ClothingItem>
 */
class ClothingItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClothingItem::class);
    }

    /**
     * @param BodyZone $zone
     * @return array<int, ClothingItem>
     */
    public function findByBodyZone(BodyZone $zone): array
    {
        /** @var array<int, ClothingItem> $result */
        $result = $this->createQueryBuilder('c')
            ->join('c.subCategory', 's')
            ->join('s.category', 'cat')
            ->where('cat.bodyZone = :zone')
            ->setParameter('zone', $zone)
            ->getQuery()
            ->getResult();

        return $result;
    }

    /**
     * @param UserInterface $user
     * @return array<int, ClothingItem>
     */
    public function findByOwner(UserInterface $user): array
    {
        /** @var array<int, ClothingItem> $result */
        $result = $this->createQueryBuilder('c')
            ->where('c.owner = :user')
            ->setParameter('user', $user)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $result;
    }

    /**
     * @param UserInterface $user
     * @return array<int, ClothingItem>
     */
    public function findUnreviewedByOwner(UserInterface $user): array
    {
        /** @var array<int, ClothingItem> $result */
        $result = $this->createQueryBuilder('c')
            ->where('c.owner = :user')
            ->andWhere('c.subCategory IS NULL')
            ->andWhere('c.status != :complete')
            ->setParameter('user', $user)
            ->setParameter('complete', ClothingItemStatus::COMPLETE)
            ->orderBy('c.createdAt', 'ASC')
            ->getQuery()
            ->getResult();

        return $result;
    }

    //    /**
    //     * @return ClothingItem[] Returns an array of ClothingItem objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ClothingItem
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
