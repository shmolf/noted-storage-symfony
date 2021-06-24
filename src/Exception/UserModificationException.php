<?php

namespace App\Exception;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserModificationException extends HttpException implements ErrorList
{
    private array $errors;

    public function __construct(
        int $statusCode = 400,
        string $message = null,
        Exception $previous = null,
        array $headers = [],
        ?int $code = 0
    ) {
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function setErrors(array $errors): ErrorList
    {
        $this->errors = $errors;

        return $this;
    }
}
