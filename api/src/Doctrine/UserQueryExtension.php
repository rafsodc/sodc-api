<?php
// api/src/Doctrine/CurrentUserExtension.php

namespace App\Doctrine;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

final class UserQueryExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null): void
    {
        if (User::class !== $resourceClass || $this->security->isGranted('ROLE_ADMIN') || null === $this->security->getUser()) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(sprintf('%s.isMember = :isMember', $rootAlias));
        $queryBuilder->setParameter('isMember', 'TRUE');
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, string $operationName = null, array $context = []): void
    {
        if (User::class !== $resourceClass || $this->security->isGranted('ROLE_ADMIN') || null === $user = $this->security->getUser()) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        // Return results if they are a member and sharing details, or if it's this user
        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->andX(
                    sprintf('%s.isMember = :isMember', $rootAlias),
                    sprintf('%s.isShared = :isShared', $rootAlias)
                ),
                sprintf('%s.id = :id', $rootAlias)
            )
        );
        $queryBuilder->setParameter('isMember', 'TRUE');
        $queryBuilder->setParameter('isShared', 'TRUE');
        $queryBuilder->setParameter('id', $user->getId());
    }
}
