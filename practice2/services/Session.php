<?php

namespace app\services;

class Session {
    const USER_ID = 'user_id';
    private $params;

    public function __construct(array $params = []) {
        session_start();
        if ($params) {
            $_SESSION = array_merge($_SESSION, $params);
        }
        $this->fill();
    }

    /**
     * Копирование занчений из массива SESSION
     */
    private function fill() {
        $this->params = $_SESSION;
    }

    public function destroy() {
       session_destroy();
    }

    /**
     * @param string|null $key
     * @return array|string|int|object|null
     */
    public function get(string $key = null ){
        if ($key) {
            return $this->params[$key]?? null;
        }
        return $this->params;
    }

    /**
     * Добавление параметров в сессию
     * @param array $params
     */
    public function add(array $params) {
        foreach ($params as $k => $v) {
            $_SESSION[$k] = $v;
        }
        $this->fill();
    }

    /**
     * Удаление значения из сессии
     * @param string $key
     */
    public function remove(string $key) {
       unset($_SESSION[$key]);
        $this->fill();
    }
}