<?php

namespace App\Repository;

use App\Entity\IPGReturn;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method IPGReturn|null find($id, $lockMode = null, $lockVersion = null)
 * @method IPGReturn|null findOneBy(array $criteria, array $orderBy = null)
 * @method IPGReturn[]    findAll()
 * @method IPGReturn[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IPGReturnRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IPGReturn::class);
    }

    // /**
    //  * @return IPGReturn[] Returns an array of IPGReturn objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?IPGReturn
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
