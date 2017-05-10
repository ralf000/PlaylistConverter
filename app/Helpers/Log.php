<?php

namespace App\Helpers;

use App\Contracts\AFile;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Log extends AFile
{
    const LOG_NAME = 'info';
    const LOGS_NUM = 20;

    /**
     * Log constructor.
     */
    public function __construct()
    {
        $path = storage_path() . '/logs/' . self::LOG_NAME . '.txt';
        if (!@fopen($path, 'r')) {
            fclose(fopen($path, 'w'));
        }
        parent::__construct($path);
    }


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


    public function getLogs()
    {
        $logsCounter = 0;
        $logs = array_reverse(file($this->path));
        $output = [];
        foreach ($logs as $index => $log) {
            $output[] = $this->logHandler($log);
            if ($index === self::LOGS_NUM - 1)
                break;
        }
        return $output;
        /*while ($logsCounter < self::LOGS_NUM && !feof($this->descriptor)) {
            $line = fgets($this->descriptor);
            if (!$line) continue;
            $logs[] = $this->logHandler($line);
            $logsCounter++;
        }
        return array_reverse($logs);*/
    }

    /**
     * Парсит строку логов
     *
     * @param string $log
     * @return array
     */
    private function logHandler(string $log) : array
    {
        $log = MbString::mb_trim($log);
        preg_match('~^\[(.*)\]~Uu', $log, $date);
        preg_match('~: (.*) \[\]~Uu', $log, $message);
        $date = implode("<br>", explode(' ', last($date)));

        return ['date' => $date, 'message' => last($message)];
    }

    /**
     * @param $message
     * @return bool
     */
    private static function initCustomLogger(string $message) : bool
    {
        $logger = new Logger(self::LOG_NAME);
        $logger->pushHandler(new StreamHandler((new self)->path), Logger::INFO);

        return $logger->addInfo($message);
    }
}