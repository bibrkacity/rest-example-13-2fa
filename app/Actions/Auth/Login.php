<?php

namespace App\Actions\Auth;

use App\Exceptions\ApiException;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Responses\LoginResponse;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class Login
{
    public function handle(LoginRequest $request): LoginResponse
    {

        $email = trim($request->input('email'));
        $password = trim($request->input('password'));

        $query = User::query()
            ->where('email', $email);
        $user = $query->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw new ApiException('Invalid login or password', ResponseAlias::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken('start');

        return new LoginResponse($user, $token->plainTextToken);
    }
}
