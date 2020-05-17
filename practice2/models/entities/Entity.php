<?php

namespace app\models\entities;

abstract class Entity {
    /**
     * Заполнение модели параметрами
     * @param array $data
     */
    public function load(array $data) {
        foreach ($data as $k=>$v) {
            if (!is_null($v) && trim($v) !== '') {
                $this->$k = $v;
            }
        }
    }
}