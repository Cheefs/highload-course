<?php

namespace app\models;

class Product {
    private $id;
    private $name;
    private $count;
    private $price;

    public function __construct( string $name = 'no name', int $count = 1, int $price = 0 ) {
        $this->name = $name;
        $this->count = $count;
        $this->price = $price;
    }

    public function getId() {
        return $this->id;
    }

    public function getCount() {
        return $this->count;
    }

    public function setCount( string $count ) {
        $this->count = $count;
        return $this;
    }

    public function getName() {
        return $this->name;
    }

    public function getPrice() {
        return $this->price;
    }
}
