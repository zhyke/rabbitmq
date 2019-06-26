<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$queue = 'rpc_queue';
$channel->queue_declare($queue, false, false, false, false);

$callback = function($req)
{
    echo ' Received '.$req->body. "\n";
    $msg = new AMQPMessage(
        'done',
        array('correlation_id' => $req->get('correlation_id'))
        );
    $req->delivery_info['channel']->basic_publish($msg, '', $req->get('reply_to'));
    
    $req->delivery_info['channel']->basic_ack($req->delivery_info['delivery_tag']);   
    echo " Done\n";
};

$channel->basic_qos(null, 1, null);
// true:不开启ack模式， false：开启ack模式
$no_ack = false;

$channel->basic_consume($queue, '', false, $no_ack, false, false, $callback);

while (count($channel->callbacks)) {
    $channel->wait();
}
$channel->close();
$connection->close();
