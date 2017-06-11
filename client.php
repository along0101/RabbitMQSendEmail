<?php

require __DIR__ . '/vendor/autoload.php';
require 'config.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

//如果你使用docker容器rabbitmq，启动之时把它的端口投射到5672即可通过localhost访问了，否则需要稍微调整
//先建立连接
$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');

//初始化通道channel
$channel = $connection->channel();

//创建队列
//参数对应：$queue, $passive, $durable, $exclusive, $auto_delete, $nowait, $arguments, $ticket
$channel->queue_declare('sendEmail', false, false, false, false);

//提示CTRL+C退出死循环
echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

/**
 * 初始化phpmailer
 * @global array $config 配置
 * @param string $mailerType
 * @return \PHPMailer
 */
function initial_mailer($mailerType = 'smtp') {
    global $config;
    $mail = new PHPMailer();
    $mail->CharSet = "UTF-8";
    switch ($mailerType) {
        case 'mail':
            $mail->IsMail();
            break;
        case 'sendmail':
            $mail->IsSendmail();
            break;
        case 'qmail':
            $mail->IsQmail();
            break;
        case 'smtp':
        default :
            $mail->IsSMTP(); // 使用SMTP方式发送
            $mail->SMTPAuth = true; // 启用SMTP验证功能 
            break;
    }
    $mail->Host = $config['host']; // 您的企业邮局域名
    $mail->Username = $config['username']; // 邮局用户名(请填写完整的email地址)     
    $mail->Password = $config['userpass']; // 邮局密码     
    $mail->Port = $config['port'];
    $mail->setFrom($config['fromAddr'], $config['fromName']);
    return $mail;
}

/**
 * 通过phpmailer发送邮件
 * @param string $to 目标邮箱地址
 * @param string $content 内容
 * @return boolean 是否成功
 */
function send_active_mail($to, $content) {
    $mail = initial_mailer();
    $subject = '这是一封测试邮件，来自本地。';
    //收件人地址，格式是AddAddress("收件人email","收件人姓名")
    $mail->AddAddress($to, explode('@', $to)[0]);
    //$mail->AddReplyTo("", "");     
    //$mail->AddAttachment("/var/tmp/file.tar.gz"); // 添加附件     
    $mail->IsHTML(true); // set email format to HTML //是否使用HTML格式     

    $mail->Subject = $subject; //邮件标题     
    $mail->Body = $content; //邮件内容       
    if (!$mail->Send()) {
        return false;
    }
    return true;
}

/**
 * 由于rabbitmq传递的是一条字符串消息，所以我们传递的信息以“|”号分割邮箱和内容
 * 格式：emailAddr|contenString
 */
$callback = function($msg) {
    list($email, $emailHtml) = explode('|', $msg->body);
    if (send_active_mail($email, $emailHtml)) {
        //成功发送
        echo " [x] Received ", $msg->body, ">send email successful\n";
    } else {
        //失败处理
        echo " [x] Received ", $msg->body, ">send email fail\n";
    }
};

$channel->basic_consume('sendEmail', '', false, true, false, false, $callback);

while (count($channel->callbacks)) {
    $channel->wait();
}


