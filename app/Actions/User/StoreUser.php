<?php

namespace App\Actions\User;

use App\Http\Requests\User\StoreRequest;
use App\Models\User;

class StoreUser
{
    public function handle(StoreRequest $request): array
    {
        $attributes = $request->validated();

        $user = new User($attributes);

        $user->save();

        return $user->toArray();
    }
}
