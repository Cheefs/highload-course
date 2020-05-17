<?php


namespace app\models\entities;

/**
 * @property string $first_name
 * @property string $last_name
 * @property string $second_name
 * @property string $email
 * @property string $phone
 *
 */
class User extends Entity {
    public $username;
    public $password;

    public $first_name;
    public $last_name;
    public $second_name;
    public $email;
    public $phone;
    public $is_guest = false;
}