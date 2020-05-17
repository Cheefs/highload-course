<?php

namespace app\common\traits;

trait TSingleton {
    private static $items;

    protected function __construct(){}
    protected function __clone(){}
    protected function __wakeup(){}

    /**
     * @return mixed
     */
    public static function getInstance() {
        if (is_null(static::$items)) {
            static::$items =  new static();
        }
        return static::$items;
    }
}