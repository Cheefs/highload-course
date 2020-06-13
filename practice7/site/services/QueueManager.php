<?php

namespace app\services;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class QueueManager {
    protected $active;
    protected $connection;

    /** @var $channel AMQPChannel */
    protected $channel;

    public function getChannel() {
        return $this->channel;
    }

    public function run( array $queuesList ) {
        $this->active = true;
        $this->connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $this->channel = $this->connection->channel();

        $this->addQueues( $queuesList );
    }

    public function stop() {
        $this->active = false;
        $this->channel->close();
        $this->connection->close();
    }

    public function listen( $queue, $callbackCnxt ) {
        $this->channel->basic_consume( $queue, '', false, true, false, false, $callbackCnxt );
    }

    public function sendMessage( array $data, string $queue ) {
        $this->channel->queue_declare($queue, true, true, false, false);
        $msg = new AMQPMessage( json_encode($data) );
        $this->channel->basic_publish( $msg, '', $queue );
    }

    public function getActive() {
        return $this->active;
    }

    protected function addQueues( array $queuesList ) {
        foreach ( $queuesList as $el ) {
            $this->channel->queue_declare($el, false, true, false, false);
        }
    }
}
