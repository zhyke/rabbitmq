<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// 连接服务
$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();
$channel->queue_declare('hello', false, false, false, false);
$msg = new AMQPMessage('hello world');
$routing_key = 'hello';
$channel->basic_publish($msg,'', $routing_key);
echo " [x] Sent 'Hello World!'\n";
$channel->close();
$connection->close();