<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$exchange = 'topic_logs';
$auto_delete = false;
$channel->exchange_declare($exchange, 'topic', false, false, $auto_delete);

// 设置为true，在断开的时候会删除对应的exchange和queue
$exclusive = true;

// 不自己指定queue名称，mq会自己生成名字类似amq.gen-JzTY20BRgKO-HjmUJj0wLg
list($queue_name, ,) = $channel->queue_declare('', false, false, $exclusive, $auto_delete);
$severities = [
    '*.info',
    '*.warning',
    '*.error',
    'and.#'
];
foreach ($severities as $value)
{
    $routing_key = $value;
    $channel->queue_bind($queue_name, $exchange, $routing_key);
}

echo " [*] Waiting for messages,queue name :{$queue_name}. To exit press CTRL+C\n";

$callback = function($msg)
{
    echo ' Received '.$msg->body. "\n";
    file_put_contents('./debug.log', ' Received '.$msg->body. "\n",FILE_APPEND);
    echo ' [x] ', $msg->delivery_info['routing_key'], ':', $msg->body, "\n";   
    file_put_contents('./debug.log', " Done\n",FILE_APPEND);
    echo " Done\n";
};

// true:不开启ack模式， false：开启ack模式
$no_ack = true;

$channel->basic_consume($queue_name,'',false,$no_ack,false,false,$callback);

while (count($channel->callbacks)) {
    $channel->wait();
}
$channel->close();
$connection->close();
