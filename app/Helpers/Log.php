<?php

namespace App\Helpers;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Log
{
    const LOG_NAME = 'info';

    /**
     * Пишет логи в файл self::LOG_NAME
     * 
     * @param string $message
     * @return bool
     */
    public static function log(string $message) : bool
    {
        return self::initCustomLogger($message);
    }

    /**
     * @param $message
     * @return bool
     */
    private static function initCustomLogger(string $message) : bool
    {
        $logger = new Logger(self::LOG_NAME);
        $logger->pushHandler(new StreamHandler(storage_path() . '/logs/' . self::LOG_NAME, Logger::INFO));

        return $logger->addInfo($message);
    }
}