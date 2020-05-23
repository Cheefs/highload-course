<?php
/**
* Order
*  - id: int
*  - name: varchar
*  - date: datetime
*  - user_id: int
*  - sum: float
*/

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
     protected function runQuery($query, Order $order) {
          mysql_query($query, ShardingSingleton::getInstance()->getConnection($order));
     }

     public function insert(Order $order) {
          //добавить запись и вернуть объект с id
          $this->runQuery("insert lalalal", $order);
          return mysql_insert_id();
     }

     public function update(Order $order) {
          //обновить объект
          $this->runQuery("update lalala", $order);
     }

     public function delete(Order $order) {
          //удалить объект
          $this->runQuery("delete lalalal", $order);
     }
}

class ShardingSingleton {
    protected static $instance = null;
    protected $server1;
    protected $server2;

    protected function __construct() {
         $this->server1 = mysql_connect('server1', 'user1', 'pass1');
         $this->server2 = mysql_connect('server2', 'user2', 'pass2');
    }

    public static function getInstance() {
        if (static::$instance == null) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    public function getConnection(Order $order) {
         $server = $this->server1;
         if ($order->user_id % 2 == 0) $server = $this->server2; 
         return $server;
    }
}

$storage = new OrderStorage();

$someOrder = new Order('test order1', date('Ymd'), 1, 100);
$storage->insert($someOrder);