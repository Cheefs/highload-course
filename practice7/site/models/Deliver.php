<?php

namespace app\models;

use app\services\Log;
use app\services\QueueManager;
use PhpAmqpLib\Message\AMQPMessage;

class Deliver {
    const DELIVER_QUEUE = 'processing_delivery';
    const DELIVER_QUEUE_DONE = 'completed_delivery';

    private $queueManager;

    public function __construct( QueueManager $queueManager ) {
        $this->queueManager = $queueManager;
    }

    public function process( AMQPMessage $message ) {
        $this->queueManager->sendMessage( [
            [ 'order' => $message->body, 'date' => date('YYYY-MM-DD') ]
        ],self::DELIVER_QUEUE);
        echo 'process delivery '. PHP_EOL;
        Log::writeLog('info',"process delivery");
    }

    public function confirmDeliver( AMQPMessage $message ) {
        echo 'confirm delivery '. PHP_EOL;
        Log::writeLog('info',"confirm delivery");
        $this->queueManager->sendMessage( [
            [ 'order' => $message->body, 'date' => date('YYYY-MM-DD') ]
        ],self::DELIVER_QUEUE_DONE );
    }
    public function monitoringDelivery( AMQPMessage $message ) {
        $orderData = json_decode($message->getBody());
        /// do something
        /// just push notify
        $this->queueManager->sendMessage( $orderData, Deliver::DELIVER_QUEUE_DONE );

        Log::writeLog('info',"monitoring delivery");
        echo 'monitoring delivery '. PHP_EOL;
    }
}
