<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();
$channel->queue_declare('hello', false, false, false, false);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function($msg)
{
    file_put_contents('./debug.log', ' [x] Received '.$msg->body. "\n",FILE_APPEND);
    sleep(substr_count($msg->body, '.'));
    file_put_contents('./debug.log', " [x] Done\n",FILE_APPEND);
};

$channel->basic_consume('hello', '', false, true, false, false, $callback);

while (count($channel->callbacks)) {
    $channel->wait();
}