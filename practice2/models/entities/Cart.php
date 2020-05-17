<?php

namespace app\models\entities;
/**
 * @property int $count
 * @property int $user_id
 * @property int $product_id
 * @property int $is_guest
 *
 */
class Cart extends Entity {
    public $count;
    public $user_id;
    public $product_id;

    public function getTotalPrice($price) {
        return $this->count * $price;
    }

    public function isThisLastProduct() {
        return $this->count == 1;
    }
}