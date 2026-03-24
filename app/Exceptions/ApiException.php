<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ApiException extends Exception
{
    protected const string ERROR_CAPTION = 'error';

    protected int $statusCode;
    protected array $args {
        set {
            $this->args = $value;
        }
    }

    public function __construct(string $message, int $statusCode = ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, array $args = [])
    {
        parent::__construct(message:$message);
        $this->statusCode = $statusCode;
        $this->args = $args;
    }

    public function render(): JsonResponse
    {
        report($this);

        $message = $this->exceptionMessage();

        return new JsonResponse(data: [self::ERROR_CAPTION => $message], status: $this->statusCode, json: false);
    }

    protected function exceptionMessage(): string
    {
        $message = $this->getMessage();
        if (config('app.debug')) {
            $reply = get_class($this) . ': ' . $message;
            $reply .= '. File: ' . $this->getFile();
            $reply .= '. Line: ' . $this->getLine();
            if (!empty($this->args)) {
                $reply .= '. args: ' . var_export($this->args, true);
            }

        } else {
            $reply = $message;
        }

        return $reply;
    }
}
