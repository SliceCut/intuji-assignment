<?php

namespace App\Cores;

use App\Exceptions\ModelNotFoundException;
use App\View;
use Exception;

class BaseExceptionHandler
{
    public static function handleException($exception)
    {
        if ($exception instanceof ModelNotFoundException) {
            return View::make("errors/404_page.php", [
                "exception" => $exception
            ]);
        }
        
        // Display a custom error page
        http_response_code(500);
        echo "<h1>Something went wrong</h1>";
        echo "<p>We are experiencing technical difficulties. Please try again later.</p>";

        // Optionally, display detailed error information in development environment
        if (env('APP_ENV') === 'local') {
            echo "<p><strong>Exception:</strong> " . $exception->getMessage() . "</p>";
            echo "<p><strong>File:</strong> " . $exception->getFile() . " on line " . $exception->getLine() . "</p>";
            echo "<pre>" . $exception->getTraceAsString() . "</pre>";
        }
    }
}