<?php

namespace App\Requests;

use App\Cores\Validation;

class EventRequest extends Validation
{
    public function rules(): array
    {
        return [
            "summary" => [
                "required",
                "string"
            ],
            "description" => [
                "sometimes",
                "string"
            ],
            "start_time" => [
                "required",
                "datetime"
            ],
            "end_time" => [
                "required",
                "datetime",
                "after:start_time"
            ],
            "location" => [
                "sometimes",
                "string",
            ]
        ];
    }
}