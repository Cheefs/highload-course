<?php

namespace app\models;

use app\services\Log;
use app\services\QueueManager;

class Order {
    
    const NEW_ORDER_QUEUE = 'newOrders';
    const CONFIRM_PAYMENTS_QUEUE = 'confirmPayments';

    const GUEST_USER = '_guest_';
    
    const STATUS_NEW = 0;
    const STATUS_PAID = 1;
    const STATUS_DELIVERED = 2;

    private $id;
    protected $queueManager;
    private $products;
    private $userId = 1; // default to testing
    private $status = self::STATUS_NEW;

    public function getId() {
        return $this->id;
    }

    public function getStatus() {
        return $this->status;
    }
    public function setStatus( int $status ) {
        $this->status = $status;
        return $this;
    }
    public function getUserId() {
        return $this->userId;
    }
    public function setUserId( int $userId ) {
        $this->userId = $userId;
        return $this;
    }

    public function getProducts() {
        return $this->userId;
    }
    public function setProducts( array $products ) {
        $this->products = $products;
        return $this;
    }

    public function __construct( QueueManager $queueManager, int $userId = 1, array $products = []) {
        $this->queueManager = $queueManager;
        $this->userId = trim( $userId ) ? $this->userId : null;
        $this->products = $products;
    }

    public function upStatus() {
        $this->status += (int)( $this->status < self::STATUS_DELIVERED );
        echo "order status {$this->status} ". PHP_EOL;
        Log::writeLog('info',"order status {$this->status}");
    }
    
    public function confirm() {
        if ( !count( $this->products ) ) {
            throw new \Exception('order cannot be blank');
        }
        // save order in db
        if ( $this->save() ) {
            $msg = [
                'orderId' => $this->id,
                'user' => $this->userId ?? self::GUEST_USER,
                'products' => $this->products
            ];
            echo 'confirm order '. PHP_EOL;
            $this->queueManager->sendMessage( $msg, self::NEW_ORDER_QUEUE );
            Log::writeLog('info',"confirm order");
        }
    }
    
    public function confirmPayments( $data = []) {
        $paySuccess = true;
        /// go to billing with user Account
        /// check userData
        /// take payments for all product
        if ( !$paySuccess ) {
           throw new \Exception("pay failed by orderId: {$this->id}");
        }
        
        $msg = [];
        $this->queueManager->sendMessage( $msg, self::CONFIRM_PAYMENTS_QUEUE );
        $this->upStatus();

        echo 'confirm payment '. PHP_EOL;
        Log::writeLog('info',"confirm payment");
    }

    // mock fnction
    protected function save() {
        $this->id = rand();
        return true;
    }
}
