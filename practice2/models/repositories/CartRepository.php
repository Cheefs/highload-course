<?php

namespace app\models\repositories;

use app\models\entities\Cart;
use app\models\entities\Product;

class CartRepository extends Repository {

    public static function tableName() {
        return 'cart';
    }
    protected function getEntityName() {
       return Cart::class;
    }

    public function getProduct(int $id) {
        return (new ProductRepository)->find($id);
    }

    public function getTotalCartPrice($items) {
        $total = ['count' => 0, 'price' => 0];
        foreach ($items as $item) {
            /** @var $product Product
             *  @var $item Cart
             */
            $product = (new ProductRepository())->find($item->product_id);
            $total['count'] += $item->count;
            $total['price'] += $item->count * $product->price;
        }
        return $total;
    }
}