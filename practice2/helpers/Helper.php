<?php

namespace app\helpers;

class Helper {

    public static function getClassName($namespace) {
        $namespace = explode('\\', $namespace);
        return $namespace[count($namespace) - 1];
    }
}