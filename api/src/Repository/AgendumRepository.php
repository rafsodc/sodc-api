<?php

namespace App\Repository;

use App\Entity\Agendum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Agendum|null find($id, $lockMode = null, $lockVersion = null)
 * @method Agendum|null findOneBy(array $criteria, array $orderBy = null)
 * @method Agendum[]    findAll()
 * @method Agendum[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgendumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Agendum::class);
    }

    // /**
    //  * @return Agendum[] Returns an array of Agendum objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Agendum
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
