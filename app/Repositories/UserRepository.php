<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTO\UserDTO;
use App\Exceptions\RepositoryException;
use App\Interfaces\IUserRepository;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * The repository for the User model
 */
class UserRepository extends Repository implements IUserRepository
{
    /**
     * @throws RepositoryException
     */
    public function findByDTO(UserDTO $dto): array
    {
        $builder = $this->commonBuilder($dto);

        return $this->findByDTOToArray($builder, $dto->page, $dto->perPage);
    }

    /**
     * Count users by DTO without pagination
     * @param UserDTO $dto
     * @return int
     */
    public function countByDTO(UserDTO $dto): int
    {
        $builder = $this->commonBuilder($dto);

        return (int) $builder->count();
    }

    /**
     * Create common query builder for findByDTO and countByDTO
     * @param UserDTO $dto
     * @return Builder
     */
    private function commonBuilder(UserDTO $dto): Builder
    {
        $builder = User::query();


        if ($dto->email !== null) {
            $builder->where('email', 'like', $dto->email);
        }

        if ($dto->query !== null) {
            $builder->where(function ($query) use ($dto) {
                $query
                    ->where('name', 'like', "%$dto->query%")
                    ->orWhere('email', 'like', "%$dto->query%");
            });
        }

        $builder->orderBy($dto->sortName, $dto->sortDir);

        return $builder;
    }
}
