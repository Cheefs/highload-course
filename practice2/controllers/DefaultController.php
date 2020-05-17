<?php

namespace app\controllers;

use Translate;

class DefaultController extends Controller {

    public function actionIndex() {
        return $this->render('index', [
            'translate' => new Translate(),
        ]);
    }
}