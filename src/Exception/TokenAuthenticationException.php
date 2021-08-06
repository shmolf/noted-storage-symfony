<?php

namespace App\Exception;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class TokenAuthenticationException extends HttpException implements Throwable
{
    private array $errors = [];

    public function __construct(
        int $statusCode = 400,
        string $message = null,
        Exception $previous = null,
        array $headers = [],
        ?int $code = 0
    ) {
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }
}
