<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// 连接服务
$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// 交换机的名字
$exchange = 'topic_logs';
// 交换机类型
$type = 'topic';

$auto_delete = false;
// 定义交换机
$channel->exchange_declare($exchange, $type, false, false, $auto_delete);

$data = getmypid();
$msg = new AMQPMessage($data);
$source = [
    'and',
    'ios',
    'other'
];
$severities = [
    'info',
    'warning',
    'error',
    'other'
];
$routing_key = $source[mt_rand(0,2)].'.'.$severities[mt_rand(0,3)];
$channel->basic_publish($msg, $exchange, $routing_key);
echo " [x] Sent {$data} key {$routing_key}\n";
$channel->close();
$connection->close();
