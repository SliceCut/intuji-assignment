<?php

namespace App\Traits;

use App\Exceptions\UnauthorizedException;
use App\Exceptions\ModelNotFoundException;
use Exception;

trait ApiResponse
{
    public function successResponse(string $message = null, $data = null, int $status = 200)
    {
        $response = [
            "message" => $message,
            "data" => $data
        ];

        if (empty($data)) {
            unset($response["data"]);
        }

        if (empty($data)) {
            unset($response["message"]);
        }

        return jsonResponse($response, $status);
    }

    public function errorResponse(string $message, mixed $data = null, int $status = 500)
    {
        $response = [
            "status" => $status,
            "message" => $message,
        ];

        if ($data) {
            $response['errors'] = $data;
        }

        return jsonResponse($response, $status);
    }

    public function exceptionResponse(Exception $exception)
    {
        if ($exception instanceof UnauthorizedException) {
            return $this->errorResponse($exception->getMessage(), $exception->getCode());
        } elseif ($exception instanceof ModelNotFoundException) {
            return $this->errorResponse($exception->getMessage(), 404);
        } else {
            return $this->errorResponse(
                message: $exception->getMessage(),
                status: $exception->getCode() == 0 ? 500 : (is_int($exception->getCode()) ? $exception->getCode() : 500)
            );
        }
    }
}
