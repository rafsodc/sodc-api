<?php

namespace App\Filters;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class RankFilter extends AbstractFilter
{
    protected $security;

    public function __construct(Security $security, ManagerRegistry $managerRegistry, ?RequestStack $requestStack = null, LoggerInterface $logger = null, array $properties = null, NameConverterInterface $nameConverter = null)
    {
        parent::__construct($managerRegistry, $requestStack, $logger = null, $properties = null, $nameConverter = null);

        $this->security = $security;
    }

    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        $alias = $queryBuilder->getRootAliases()[0];

        switch($property) {
            case "name":
                $where = $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->like(sprintf('LOWER(%s.rank)', $alias), ':value')
                );
                $queryBuilder
                    ->andWhere($where)
                    ->setParameter('value', "%".strtolower($value)."%");
                break;
            default:
        }
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'name' => [
                'property' => null,
                'type' => 'string',
                'required' => false,
                'openapi' => [
                    'description' => 'Search for ranks by short or long name',
                ],
            ]
        ];
    }
}
