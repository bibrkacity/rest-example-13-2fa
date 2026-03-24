<?php

namespace App\Http\Responses;

use App\Models\User;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

readonly class LoginResponse implements Responsable
{
    public function __construct(
        protected User $user,
        protected string $plainToken,
    ) {
    }

    public function toResponse($request): JsonResponse
    {
        return new JsonResponse(
            data: $this->toData(),
            status: ResponseAlias::HTTP_OK,
            json: false,
        );
    }

    protected function toData(): array
    {

        return [
            'token' => $this->plainToken,
            'required2fa' => (bool)$this->user->required2fa,
            'enabled2fa' => (bool)$this->user->google2fa_enabled,
        ];
    }
}
