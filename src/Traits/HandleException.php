<?php

namespace App\Traits;

use App\Exceptions\UnauthorizedException;
use Exception;

trait HandleException
{
    /**
     * Throw a exception if response status is not 200
     * 
     * @throws UnauthorizedException
     * @throws Exception
     */
    public function throwException($response): void
    {
        if ($response["status"] != 200) {
            if (in_array($response["status"], [401, 403])) {
                throw new UnauthorizedException(
                    message: $response["payload"]["error"],
                    code: $response["status"]
                );
            }
            throw new Exception($response["payload"]["error"] ?? "Something went wrong while trying to consume apis.", $response["status"]);
        }
    }
}
