<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// 连接服务
$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// 交换机的名字
$exchange = 'direct_logs';
// 交换机类型
$type = 'direct';

$auto_delete = false;
// 定义交换机
$channel->exchange_declare($exchange, $type, false, false, $auto_delete);

$data = getmypid();
$msg = new AMQPMessage($data);
$severities = [
    'info',
    'warning',
    'error',
    'other'
];
$routing_key = $severities[mt_rand(0,3)];
$channel->basic_publish($msg, $exchange, $routing_key);
echo " [x] Sent {$data} key {$routing_key}\n";
$channel->close();
$connection->close();
