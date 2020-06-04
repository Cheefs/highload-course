<?php

namespace app\services;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Log {
    /** @var $log Logger */
    static protected $log;

    public function __construct( $path, $name ) {
        $log = new Logger($name);

        $log->pushHandler(new StreamHandler("{$path}/{$name}.log" ));
        self::$log = $log;
    }
    static function writeLog( $level, $message ) {
        self::$log->log($level, $message);
    }
}
