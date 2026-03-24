<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AuthorizationException extends ApiException
{
    public function __construct(string $message, $statusCose = ResponseAlias::HTTP_UNAUTHORIZED, array $args = [])
    {
        parent::__construct('Auth error: '.$message, $statusCose, $args);
    }
}
