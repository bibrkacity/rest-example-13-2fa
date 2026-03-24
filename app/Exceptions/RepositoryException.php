<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class RepositoryException extends ApiException
{
    public function __construct(string $message, array $args)
    {
        parent::__construct('Repository error: '.$message, ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, $args);
    }
}
