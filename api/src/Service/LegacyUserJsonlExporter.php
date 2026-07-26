<?php

namespace App\Service;

use App\Entity\User;

final class LegacyUserJsonlExporter
{
    private LegacyUserRecordTransformer $transformer;
    private LegacyUserSchemaCounter $counter;

    public function __construct(LegacyUserRecordTransformer $transformer, LegacyUserSchemaCounter $counter)
    {
        $this->transformer = $transformer;
        $this->counter = $counter;
    }

    /**
     * @param iterable<User>         $users
     * @param callable(string): void $write
     */
    public function export(iterable $users, callable $write): array
    {
        $counts = $this->counter->emptyCounts();

        foreach ($users as $user) {
            $record = $this->transformer->transform($user);
            if (null === $record) {
                continue;
            }

            $this->counter->count($record, $counts);
            $write(json_encode($record, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)."\n");
        }

        return $counts;
    }
}
