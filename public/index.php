<?php

declare(strict_types=1);

// Start the session
session_start();

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

use App\App;
use App\Config;
use App\Cores\Container;
use App\Cores\ErrorBag;
use App\Cores\FlashMessage;
use App\Cores\Request;
use App\DB;
use App\MiddlewareRegistration;
use App\Router;

$router = new Router(new Container, new Request, new MiddlewareRegistration);
$config = new Config($_ENV);
$db = new DB($config->db);
$error = new ErrorBag();
$flashMessage = new FlashMessage;

require_once dirname(__DIR__).'/routes/web.php';

$app = new App($router, $db, $error, $flashMessage);
$app->run();