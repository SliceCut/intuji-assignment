<?php

namespace App;

use App\Cores\ErrorBag;
use App\Cores\FlashMessage;
use App\Cores\Session;
use App\Cores\Validation;

class App
{
    private Router $router;
    private static DB $db;
    private static ErrorBag $error;
    private static FlashMessage $flashMessage;

    private const ERROR_BAG = "error_bag";
    private const FLASH_SESSION = "flash_session";

    public function __construct(Router $router, DB $dB, ErrorBag $error, FlashMessage $flashMessage)
    {
        $this->router = $router;
        static::$error = $error;
        static::$db = $dB;
        static::$flashMessage = $flashMessage;

        $this->prepareData();
    }

    public static function db(): DB
    {
        return static::$db;
    }

    public static function error(): ErrorBag
    {
        return Session::get(self::ERROR_BAG, static::$error);
    }

    /**
     * Get the flash message object
     *
     * @return FlashMessage
     */
    public static function flashMessage()
    {
        return Session::get(self::FLASH_SESSION, static::$flashMessage);
    }

    public function prepareData()
    {
        /**
         * Get the session validation error data, delete session validation error data and put error in session errorbag data
         */
        $errorBag =  Session::get(Validation::SESSION_VALIDATION_ERROR, new ErrorBag());
        Session::delByKey(Validation::SESSION_VALIDATION_ERROR);
        Session::put(self::ERROR_BAG, $errorBag);

        /**
         * if errorBag has errors
         */
        if (count($errorBag->getErrors()) <= 0) {
            Session::delByKey(Validation::SESSION_OLD_VALUES);
        }

        /**
         * session flash message
         */
        $flashMessage = Session::get(FlashMessage::SESSION_FLASH_MESSAGE, new FlashMessage);
        Session::delByKey(FlashMessage::SESSION_FLASH_MESSAGE);
        Session::put(self::FLASH_SESSION, $flashMessage);
    }

    public function run()
    {
        $this->router->run();
    }
}
