<?php

namespace App\Filters;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use DateTime;

class EventDateFilter extends AbstractFilter
{
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        $alias = $queryBuilder->getRootAliases()[0];
        $now = new DateTime();

        switch($property) {
            case "bookingOpen":
                $query = $queryBuilder->expr()->between(':now', sprintf('%s.bookingOpen', $alias), sprintf('%s.bookingClose', $alias));
                $where = ($value === "true") ? $query : $queryBuilder->expr()->not($query);
                break;
            case "future":
                $query = $queryBuilder->expr()->lte(sprintf('%s.date', $alias), ':now');
                $where = ($value === "true") ? $query : $queryBuilder->expr()->not($query);
                break;
            default:
                return;
        }

        $queryBuilder
            ->andWhere($where)
            ->setParameter('now', $now->format('Y-m-d'));
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'bookingOpen' => [
                'property' => null,
                'type' => 'bool',
                'required' => false,
                'openapi' => [
                    'description' => 'Search for events by whether booking is open',
                ],
            ],
            'future' => [
                'property' => null,
                'type' => 'bool',
                'required' => false,
                'openapi' => [
                    'description' => 'Search for events in the future',
                ],
            ]
        ];
    }
}
