<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

/**
 * Base class for project exceptions
 */
class ApiException extends Exception
{
    protected const string ERROR_CAPTION = 'error';

    protected int $statusCode;

    /**
     * Additional arguments for exception for custom messages
     *
     * @var array
     */
    protected array $args {
        set {
            $this->args = $value;
        }
    }

    public function __construct(string $message, int $statusCode = ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, array $args = [])
    {
        parent::__construct(message: $message);
        $this->statusCode = $statusCode;
        $this->args = $args;
    }

    /**
     * Render the exception into an HTTP response.
     * @return JsonResponse
     */
    public function render(): JsonResponse
    {
        report($this);

        $message = $this->exceptionMessage();

        return new JsonResponse(data: [self::ERROR_CAPTION => $message], status: $this->statusCode, json: false);
    }

    protected function exceptionMessage(): string
    {
        $message = $this->getMessage();
        $reply = $message;
        if (config('app.debug')) {

            $reply .= '. Class: ';
            $reply .= $this->args['class'] ?? get_class($this);
            $reply .= '. File: ';
            $reply .= $this->args['file'] ?? $this->getFile();
            $reply .= '. Line: '.$this->getLine();
            if (! empty($this->args)) {
                $reply .= '. args: '.var_export($this->args, true);
            }
        }

        return $reply;
    }
}
