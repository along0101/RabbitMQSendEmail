<?php

//邮箱配置文件等
//使用smtp协议
global $config;
$config = [
    'host' => 'smtp.emaildomain.com',
    'port' => 25,
    'username' => 'test@emaildomain.com',
    'userpass' => 'TheEmailPassword',
    'fromName' => 'From Test Demo',
    'fromAddr' => 'test@emaildomain.com'
];

