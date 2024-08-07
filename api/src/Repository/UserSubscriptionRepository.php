<?php

namespace App\Repository;

use App\Entity\UserSubscription;
use App\Entity\User;
use App\Entity\Subscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserSubscription>
 *
 * @method UserSubscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserSubscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserSubscription[]    findAll()
 * @method UserSubscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserSubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserSubscription::class);
    }

    public function add(UserSubscription $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserSubscription $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function isUserSubscribed($userUuid, $subscriptionUuid): bool
    {
        $qb = $this->createQueryBuilder('us')
            ->select('count(us.uuid)')
            ->where('us.owner = :userUuid')
            ->andWhere('us.subscription = :subscriptionUuid')
            ->setParameter('userUuid', $userUuid)
            ->setParameter('subscriptionUuid', $subscriptionUuid);

        return (bool) $qb->getQuery()->getSingleScalarResult();
    }

    public function findOneByUserAndSubscription(User $user, Subscription $subscription): ?UserSubscription
    {
        return $this->createQueryBuilder('us')
            ->andWhere('us.owner = :user')
            ->andWhere('us.subscription = :subscription')
            ->setParameter('user', $user)
            ->setParameter('subscription', $subscription)
            ->getQuery()
            ->getOneOrNullResult();
    }

//    /**
//     * @return UserSubscription[] Returns an array of UserSubscription objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UserSubscription
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
