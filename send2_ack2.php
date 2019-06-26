<?php
// php send2_ack.php abc.zyk.aaa
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// 连接服务
$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$queue = 'task_queue';
// 将queue设置成持久的  
$durable = true;
$channel->queue_declare($queue, false, $durable, false, false);
$data = implode(' ', array_slice($argv, 1)). getmypid();
// 将消息设置成持久的
$properties = [
    'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
];
$msg = new AMQPMessage($data,$properties);
$channel->basic_publish($msg,'',$queue);
echo " [x] Sent {$data}\n";
$channel->close();
$connection->close();
