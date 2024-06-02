<?php

namespace App\Requests;

use App\Cores\Validation;

class EventFilterRequest extends Validation
{
    public function rules(): array
    {
        return [
            "per_page" => [
                "sometimes",
                "integer"
            ],
            "search" => [
                "sometimes",
                "string"
            ],
            "start_time" => [
                "sometimes",
                "datetime"
            ],
            "end_time" => [
                "sometimes",
                "datetime",
                "after:start_time"
            ]
        ];
    }
}
