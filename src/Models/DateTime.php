<?php

namespace App\Models;

class DateTime
{
    public string $dateTime;
    public string $timeZone;

    public function __construct(string $dateTime, string $timeZone)
    {
        $this->dateTime = $dateTime;
        $this->timeZone = $timeZone;
    }

    public function offsetGet($offset)
    {
        return $this->$offset;
    }
    
    public function __get($name)
    {
        return $this->messages[$name] ?? "";
    }
}
