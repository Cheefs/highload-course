<?php

namespace app\controllers;

use app\models\entities\Cart;
use app\models\repositories\CartRepository;
use app\models\repositories\ProductRepository;
use app\services\Session;

class CartController extends Controller {

   public function actionIndex() {
       $user_id = $this->session(Session::USER_ID);
       $repository = new CartRepository();
       if ($user_id) {
           $cartItems = $repository->findAll(['user_id' => $user_id]);
       }

       return $this->render('index', [
           'cartItems' => $cartItems ?? [],
           'controller' => $this->getControllerName(),
           'productRepository' => new ProductRepository(),
           'cartTotal' => $repository->getTotalCartPrice($cartItems?? []),
       ]);
   }

   public function actionDelete(int $id) {
        $repository = new CartRepository();
        $item = $repository->find($id);
        /* @var $item Cart */
        if ($item) {
            $isLast = $item->isThisLastProduct();
            if ( !$isLast && !$this->get('all') ) {
                $item->count--;
                $repository->save($item);
            } else {
                $repository->delete($item);
            }
        }
        return $this->redirect();
   }
}