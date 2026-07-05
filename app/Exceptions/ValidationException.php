<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ValidationException extends ApiException
{
    public function __construct($message)
    {
        parent::__construct($message, ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
    }

    protected function exceptionMessage(): string
    {
        $reply = $this->message;
        if (config('app.debug')) {
            $reply .= '. Class: Illuminate\Validation\ValidationException';
        }

        return $reply;
    }
}
