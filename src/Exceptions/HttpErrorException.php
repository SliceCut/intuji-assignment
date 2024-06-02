<?php

namespace App\Exceptions;

use Exception;

class HttpErrorException extends Exception
{
    private array $error;

    public function __construct(string $message, int $code, array $error = [])
    {
        parent::__construct(
            message: $message,
            code: $code
        );
    }

    public function getError(): array
    {
        return $this->error;
    }
}
