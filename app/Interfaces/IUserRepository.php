<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\DTO\UserDTO;

interface IUserRepository
{
    public function findByDTO(UserDTO $dto): array;

    public function countByDTO(UserDTO $dto): int;
}
