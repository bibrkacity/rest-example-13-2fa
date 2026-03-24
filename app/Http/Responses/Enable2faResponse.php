<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

readonly class Enable2faResponse implements Responsable
{
    public function __construct(
        protected string $secret,
        protected string $qrCodeInline,
        protected string $qrUrl,
    ) {
    }

    public function toResponse($request): JsonResponse
    {
        return new JsonResponse([
            'secret' => $this->secret,
            'qrCodeInline' => $this->qrCodeInline,
            'qrUrl' => $this->qrUrl,
        ], ResponseAlias::HTTP_OK);
    }
}
