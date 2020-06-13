
<?php

require( __DIR__ .'/vendor/autoload.php');

use app\models\Deliver;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPProtocolChannelException;

use app\models\Order;
use app\models\Product;
use app\services\QueueManager;
use app\services\Log;


(new Log(__DIR__, 'app'));

/// mock
$confirm = true;
$products = [
    new Product( 'test', 5 , 100 )
];

const APP_QUEUES = [
    Order::NEW_ORDER_QUEUE,
    Order::CONFIRM_PAYMENTS_QUEUE,
    Deliver::DELIVER_QUEUE,
    Deliver::DELIVER_QUEUE_DONE
];

echo 'starting' . PHP_EOL;
Log::writeLog('info',"app start");

try {
    $queueManager = new QueueManager();
    if ( !$queueManager->getActive() ) {
        $queueManager->run( APP_QUEUES );
    }
    // соединяемся с RabbitMQ
    $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');

    $order = new Order( $queueManager );
    $deliver = new Deliver( $queueManager );

    /// для офомления заказа нужен пользоваель, просим зарегестритьватся
    if ( $confirm && count( $products ) && $order->getUserId() ) {
        $order->setProducts( $products )->confirm();
    } else if ( !$order->getUserId() ) {
        /// redirect to register\auth
    } else {
        /// do nothing or checkProducts
    }

    /// listen queues
    $queueManager->listen( Order::NEW_ORDER_QUEUE, [ $order, 'confirmPayments'] );
    $queueManager->listen( Order::CONFIRM_PAYMENTS_QUEUE, [ $deliver, 'process'] );
    $queueManager->listen( Deliver::DELIVER_QUEUE, [ $deliver, 'monitoringDelivery']);
    $queueManager->listen( Deliver::DELIVER_QUEUE_DONE, [ $order, 'upStatus']);

    while ( $order->getStatus() !== Order::STATUS_DELIVERED) {
        $queueManager->getChannel()->wait();
    }

    if ( $order->getStatus() === Order::STATUS_DELIVERED ) {
        /// send email to customer with feedbackForm
         echo 'order complete' . PHP_EOL;
         Log::writeLog('info',"order complete");
    }

    $queueManager->stop();
    echo 'stopping' . PHP_EOL;
    Log::writeLog('info',"app stop");
}
catch (AMQPProtocolChannelException $e){
    echo $e->getMessage();
}
catch (AMQPException $e){
    echo $e->getMessage();
}
?>

