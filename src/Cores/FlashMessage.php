<?php

namespace App\Cores;

class FlashMessage
{
    private array $messages = [];

    public const SESSION_FLASH_MESSAGE = "session_flash_message";

    public function setMessage(string $key, string $message)
    {
        $this->messages[$key] = $message;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function has($key): bool
    {
        return (bool) ($this->messages[$key] ?? null);
    }

    public function __get($name)
    {
        return $this->messages[$name] ?? "";
    }
}
