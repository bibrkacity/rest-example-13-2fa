<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ObjectNotFoundException extends ApiException
{
    public function __construct($message = "Object not found")
    {
        parent::__construct($message, ResponseAlias::HTTP_BAD_REQUEST);
    }
}
