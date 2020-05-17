<?php

namespace app\models\repositories;

use app\models\entities\User;

class UserRepository extends Repository {

    /**
     * @return mixed|string
     */
    public static function tableName() {
        return 'users';
    }
    protected function getEntityName() {
       return User::class;
    }

    /**
     * Получение корзины пользователя
     * @return array
     */
    public function getCart() {
        return CartRepository::findAll([
            'user_id' => $this->id,
        ]);
    }
}