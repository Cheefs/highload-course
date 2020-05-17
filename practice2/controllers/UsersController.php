<?php

namespace app\controllers;

use app\models\entities\User;
use app\models\repositories\UserRepository;
use Translate;

class UsersController extends Controller {

    /**
     * Список пользователей
     * @return false|string
     */
    public function actionIndex() {
        $users = (new UserRepository)->findAll();

        return $this->render('index', [
            'users' => $users,
            'controller' => $this->getControllerName()
        ]);
    }

    /**
     * Просмотр иинформации о пользователе
     * @param $id
     * @return false|string
     */
    public function actionView($id) {
        if (!is_null($id) && is_numeric($id)) {
            $user = (new UserRepository)->find($id);
            if ($user) {
                return $this->render('view', [
                    'user' => $user,
                    'controller' => $this->getControllerName(),
                ]);
            }
        }
        return $this->notFound();
    }

    /**
     * Удаление пользователя из базы
     * @param $id
     */
    public function actionDelete($id) {
        $repository = new UserRepository();
        $user = $repository->find($id);
        if ($user) {
            $repository->delete($user);
        }
        $this->redirect('index');
    }

    /**
     * Сохранение и обновление пользователя
     * @param null $id
     * @return false|string
     */
    public function actionSave($id = null) {
        $repository = new UserRepository();
        $user = $id? $repository->find($id): new User();

        if ($_SERVER['REQUEST_METHOD'] == POST && count($_POST)) {
            $user->load($_POST);
            $repository->save($user);
            return $this->redirect('index');
        }

        return $this->render('form', [
            'user' => $user,
            'controller' => $this->getControllerName(),
        ]);
    }
}
