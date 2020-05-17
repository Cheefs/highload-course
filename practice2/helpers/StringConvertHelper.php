<?php

namespace app\helpers;
/**
 * Вспомогательный класс, который будет хранить функции для работы с строками
 */
class StringConvertHelper {
    /**
     * Перебор всех входяших параметров, и конвертация их в camelCase
     * @param array $data
     * @return array
     */
    public static function snakeCaseToCamelCase(array $data) {
        $convertedArr = [];
        foreach ($data as $k => $v) {
            $k = self::getCamelCase($k);
            $convertedArr[$k] = $v;
        }
        return $convertedArr;
    }

    /**
     * Конвертирование строки из snake_case в camelCase
     * @param string $snake_case
     * @return mixed|string
     */
    public static function getCamelCase(string $snake_case) {
        $result = $snake_case;
        $arr = explode('_', $result);
        if (is_array($arr) && count($arr)) {
            $result = $arr[0];

            for ($i = 1; $i < count($arr); $i++) {
                $result .= ucfirst( $arr[$i] );
            }
        }
        return $result;
    }

    /**
     * @param $str
     * @return string
     */
    public static function camelCaseToSnakeCase($str) {
        $k = str_split($str);
        $resStr = '';
        foreach ($k as $char) {
            if (preg_match('/[A-Z]/', $char)) {
                $resStr .= '_'. strtolower($char);
            } else {
                $resStr .= $char;
            }
        }
        return $resStr;
    }
}