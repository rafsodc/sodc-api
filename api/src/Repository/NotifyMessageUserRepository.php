<?php

namespace App\Repository;

use App\Entity\NotifyMessageUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NotifyMessageUser>
 *
 * @method NotifyMessageUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method NotifyMessageUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method NotifyMessageUser[]    findAll()
 * @method NotifyMessageUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotifyMessageUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotifyMessageUser::class);
    }

    public function add(NotifyMessageUser $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(NotifyMessageUser $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function save(NotifyMessageUser $notifyMessageUser): void
    {
        $this->getEntityManager()->persist($notifyMessageUser);
        $this->getEntityManager()->flush();
    }

//    /**
//     * @return NotifyMessageUser[] Returns an array of NotifyMessageUser objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?NotifyMessageUser
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
