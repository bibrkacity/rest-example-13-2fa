<?php

namespace App\Actions\User;

use App\Exceptions\ObjectNotFoundException;
use App\Http\Requests\User\UpdateRequest;
use App\Models\User;

class UpdateUser
{
    public function handle(UpdateRequest $request, int $id): array
    {

        $user = User::find($id);
        if ($user === null) {
            throw new ObjectNotFoundException('User with id='.$id." does not exist");
        }

        $attributes = $request->validated();

        if (isset($attributes['password']) && ($attributes['password'] != '')) {
            if ($request->user()->cannot('changePassword', $user)) {
                unset($attributes['password']);
            }
        }

        $user->fill($attributes);
        $user->save();

        return $user->toArray();
    }
}
