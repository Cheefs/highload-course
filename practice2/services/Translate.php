<?php

/**
 * Класс для переводов
*/
class Translate {

    /**
     * @param string $lang
     * @param string $message
     * @return mixed|string
     */
    public static function t(string $lang, string $message) {
        $translate = include MESSAGES.$lang.FILE_EXT;
        return $translate[$message]?? $message;
    }
}