<?php

namespace app\controllers;

use app\models\entities\Cart;
use app\models\repositories\CartRepository;
use app\models\repositories\ProductRepository;
use app\services\Session;

class ProductsController extends Controller {

    public function actionIndex() {
        $products = (new ProductRepository)->findAll();
        return $this->render('index', [
            'products' => $products,
            'controller' => $this->getControllerName(),
        ]);
    }

    public function actionView($id) {
        if (!is_null($id) && is_numeric($id)) {
            $product = (new ProductRepository)->find($id);
            if ($product) {
                return $this->render('view', [
                    'product' => $product,
                    'controller' => $this->getControllerName(),
                ]);
            }
        }
        return $this->notFound();
    }

    public function actionDelete(int $id) {
        $repository = new ProductRepository();
        $product = $repository->find($id);
        if ($product) {
            $repository->delete($product);
        }
        $this->redirect('index');
    }

    public function actionBuy(int $id) {
        $repository = new CartRepository();
        $user_id = $this->session(Session::USER_ID);

        $cartItem = $repository->findAll(['product_id' => $id, 'user_id' => $user_id])[0];
        if (!$cartItem) {
            $cartItem = new Cart();
            $cartItem->user_id = $user_id;
            $cartItem->product_id = $id;
            $cartItem->count = 1;
        } else {
            $cartItem->count++;
        }
        $repository->save($cartItem);

        return $this->redirect();
    }
}
