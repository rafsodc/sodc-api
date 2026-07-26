<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final class LegacyUserExportSource
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /** @return iterable<User> */
    public function users(): iterable
    {
        return $this->entityManager->createQueryBuilder()
            ->select('user', 'rank', 'userSubscription', 'subscription')
            ->from(User::class, 'user')
            ->leftJoin('user.rank', 'rank')
            ->leftJoin('user.userSubscriptions', 'userSubscription')
            ->leftJoin('userSubscription.subscription', 'subscription')
            ->orderBy('user.uuid', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
