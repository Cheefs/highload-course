<?php

class Order {
     public $id;
     public $name;
     public $date;
     public $user_id;
     public $sum;

     public function __construct($name, $date, $user_id, $sum) {
          $this->name = $name;
          $this->date = $date;
          $this->user_id = $user_id;
          $this->sum = $sum;
     }
}

class OrderStorage {
    /**
     * @param Order $order
     * @return mysqli
    **/
    protected function runQuery(Order $order) {
        $link = ShardingManager::getConnection($order);
        mysqli_query( $link, $order );

        return $link;
    }

    public function insert(Order $order) {
        return mysqli_insert_id(
            $this->runQuery( $order )
        );
    }

    public function update(Order $order) {
        //обновить объект
        $this->runQuery( $order );
    }

    public function delete(Order $order) {
        //удалить объект
        $this->runQuery( $order );
    }
}


/**
 * 
 * Как я понял всю суть шардинга, это разделения данных по разным серверам
 * и в коде мы сами рапределяем кто в какой шард ходит, в нашем примере и во многих
 * статьях показывают пример по остатку от деления id пользователя, и в соответсвии с ним
 * выбирают номер шарда. 
 * 
 * В моем случае будет $user_id % count( SHARDS_CONFIG );
 * 
 * Хорошие стати по шардингу и репликациям, читал пока разбирался в сути задачи:
 * @link https://ruhighload.com/Горизонтальный+шардинг
 * @link https://ruhighload.com/Шардинг+и+репликация
 * @link https://ruhighload.com/Репликация+данных
 **/


class ShardingManager {
    const DEFAULT_SHARD_INDEX = 0;
    const SHARDS_CONFIG = [
        [ 'address' => '10.0.2.7', 'username' => 'root', 'password' => 'root' ],
        [ 'address' => '10.0.2.8', 'username' => 'root', 'password' => 'root' ]
    ];

    public static function getConnection( Order $order ) {
     $shardsCount = count( self::SHARDS_CONFIG );
     return self::setConfig(
         self::SHARDS_CONFIG[ $order->user_id % $shardsCount ] ?? self::SHARDS_CONFIG[ self::DEFAULT_SHARD_INDEX ]
      );
    }

    protected static function setConfig( array $selectedConfig ) {
        return mysqli_connect(
            $selectedConfig['address'] ,
            $selectedConfig['username'],
            $selectedConfig['password']
        );
    }
}

$storage = new OrderStorage();

$someOrder = new Order('test order1', date('Ymd'), 1, 100);
$storage->insert($someOrder);