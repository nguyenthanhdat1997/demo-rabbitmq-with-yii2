<?php

namespace app\commands;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use yii\console\Controller;

class ReceiveController extends Controller
{
    /**
     * @throws \ErrorException
     */
    public function actionIndex()
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

            // Create a callback function
            $callback = function ($msg) {
                echo 'Received message: ', $msg->body, "\n";
            };

            // Consume the queue
            $channel->basic_consume($queueName, '', false, true, false, false, $callback);

            // Keep listening for messages as long as the channel has callbacks
            while (count($channel->callbacks)) {
                $channel->wait(null, false, 60);
            }

            $channel->close();
            $connection->close();
        } catch (\PhpAmqpLib\Exception\AMQPTimeoutException $e) {
            echo 'Caught timeout exception: ', $e->getMessage(), "\n";
        } catch (\Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }
}