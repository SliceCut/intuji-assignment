<?php

namespace App\Cores;

class ErrorBag
{

    private array $errors = [];

    public function setErrors(array $errors = [])
    {
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getFirstError(string $key): string
    {
        return $this->errors[$key][0] ?? "";
    }

    public function exist(string $key): bool
    {
        return (bool) ($this->errors[$key] ?? null);
    }
}
