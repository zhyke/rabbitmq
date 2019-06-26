<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// 连接服务
$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// 交换机的名字
$exchange = 'logs';
// 交换机类型
$type = 'fanout';

$auto_delete = false;
// 定义交换机
$channel->exchange_declare($exchange, $type, false, false, $auto_delete);

// 如果这里又设置了一个queue，在发送的时候会多出一条queue
//list($queue_name, ,) = $channel->queue_declare('');
//echo "random queue name :{$queue_name}";

$data = getmypid();
$msg = new AMQPMessage($data);

$channel->basic_publish($msg, $exchange, '');
echo " [x] Sent {$data}\n";
$channel->close();
$connection->close();
