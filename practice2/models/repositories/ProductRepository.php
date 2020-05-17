<?php

namespace app\models\repositories;

use app\models\entities\Product;

class ProductRepository extends Repository {
    /**
     * Функция для установки названия таблици базы данных для класса
     * @return mixed
     */
    public static function tableName() {
        return 'products';
    }

    protected function getEntityName(){
        return Product::class;
    }
}