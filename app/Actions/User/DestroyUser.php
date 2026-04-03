<?php

namespace App\Actions\User;

use App\Exceptions\ObjectNotFoundException;
use App\Models\User;

class DestroyUser
{
    public function handle(int $id): void
    {

        $user = User::find($id);

        if ($user === null) {
            throw new ObjectNotFoundException('User with id='.$id." does not exist");
        }

        $user->delete();
    }
}
