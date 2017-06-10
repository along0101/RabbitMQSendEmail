<?php

require './vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

//$connection = new PhpAmqpLib\Connection\AMQPStreamConnection($host, $port, $user, $password, $vhost, $insist, $login_method, $login_response, $locale, $connection_timeout)

//参数说明：
$connection = new AMQPStreamConnection('rabbit.host', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('hello', false, false, false, false);

$msg = new AMQPMessage('Hello World!');
$channel->basic_publish($msg, '', 'hello');

echo " [x] Sent 'Hello World!'\n";

//$channel->close();
//$connection->close();