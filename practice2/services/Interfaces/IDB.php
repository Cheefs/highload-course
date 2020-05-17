<?php

namespace app\services\interfaces;

interface IDB {
    public function find(string $sql, string $class = null, array $params = []);
    public function findAll(string $sql, string $class = null, array  $params = []);
}