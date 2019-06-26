<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();
//将queue设置成持久的
$durable = true;
//$queue = 'hello'; //对应send2.php的queue
$queue = 'task_queue';
$channel->queue_declare($queue, false, $durable, false, false);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function($msg)
{
    $rand = mt_rand(10000,99999999);
    echo $rand.' Received '.$msg->body. "\n";
    file_put_contents('./debug.log', $rand.' Received '.$msg->body. "\n",FILE_APPEND);
    sleep(5);
    file_put_contents('./debug.log', $rand." Done\n",FILE_APPEND);
    echo $rand." Done\n";
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};

$channel->basic_qos(null, 1, null);
// true:不开启ack模式， false：开启ack模式
$no_ack = false;

$channel->basic_consume($queue,'',false,$no_ack,false,false,$callback);

while (count($channel->callbacks)) {
    $channel->wait();
}
$channel->close();
$connection->close();
