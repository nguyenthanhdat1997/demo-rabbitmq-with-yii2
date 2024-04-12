<?php

namespace app\commands;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use yii\console\Controller;

class SendController extends Controller
{
    public function actionIndex($msg)
    {
        try {
            // Create a connection
            $connection = new AMQPStreamConnection(
                'octopus.rmq3.cloudamqp.com', // host
                5672, // port
                'icwhxval', // user
                'CFHN_60-doZgv4ympRSu69KRWjkBBwx3', // password
                'icwhxval', // vhost
            );
            // Create a channel
            $channel = $connection->channel();

            $queueName = 'q1';

            // Declare a queue
            $channel->queue_declare($queueName, false, true, false, false);

            // Create a message
            $message = new AMQPMessage(
                $msg,
                ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,'expiration' => 60000] // 10000 ms = 10 seconds
            );
            // Send the message
            $channel->basic_publish($message, '', $queueName);

            // Close the channel and the connection
            $channel->close();
            $connection->close();
        } catch (\Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }
}
