<?php

require './vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

//参数说明：($host, $port, $user, $password, $vhost, $insist, $login_method, $login_response, $locale, $connection_timeout)
$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');

//初始化channel
$channel = $connection->channel();

//参数：$queue, $passive, $durable, $exclusive, $auto_delete, $nowait, $arguments, $ticket
$channel->queue_declare('sendEmail', false, false, false, false);

$emails = ['yourname@qq.com', 'yourname@163.com', 'yourname@163.com'];

foreach ($emails as $email) {
    $msg = new AMQPMessage($email . '|' . '内容：这是一个测试邮件。');
    $channel->basic_publish($msg, '', 'sendEmail');
    echo " [x] Sent email OK.\n";
    //sleep(1);
}
//关闭
$channel->close();
$connection->close();
