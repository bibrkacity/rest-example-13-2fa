<?php

namespace App\Actions\User;

use App\Exceptions\ObjectNotFoundException;
use App\Models\User;

class ShowUser
{
    public function handle(int $id): array
    {

        $user = User::find($id);
        if ($user === null) {
            throw new ObjectNotFoundException('User with id='.$id." does not exist");
        }

        return $user->toArray();
    }
}
