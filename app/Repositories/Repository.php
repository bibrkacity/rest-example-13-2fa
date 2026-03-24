<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Exceptions\RepositoryException;
use Illuminate\Database\Eloquent\Builder;
use Throwable;

/**
 * The base class for all repositories
 * The repository is responsible for the interaction with the database
 */
class Repository
{
    /**
     * @throws RepositoryException
     */
    protected function findByDTOToArray(Builder $builder, int $page, int $perPage): array
    {

        if ($perPage != 0) {
            $builder->skip(($page - 1) * $perPage)
                ->take($perPage);
        }

        try {
            $result = $builder->get();
        } catch (Throwable $e) {
            throw new RepositoryException(message:$e->getMessage(), args: ['trace' => $e->getTraceAsString()]);
        }

        $data = [];
        foreach ($result as $object) {
            $data[] = $object->toArray();
        }

        return $data;
    }
}
