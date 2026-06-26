<?php

namespace App\Repository;

use App\Entity\Color;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Color>
 */
class ColorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Color::class);
    }

    /**
     * @param list<string> $families
     * @return list<Color> Returns a list of Color objects
     */
    public function findByColorFamily(array $families): array
    {
        /** @var list<Color> $return */
        $return = $this->createQueryBuilder('c')
            ->andWhere('c.family IN (:families)')
            ->setParameter('families', $families)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(15)
            ->getQuery()
            ->getResult()
        ;

        return $return;
    }

    //    /**
    //     * @return Color[] Returns an array of Color objects
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

    //    public function findOneBySomeField($value): ?Color
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
