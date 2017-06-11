# RabbitMQSendEmail
通过RabbitMQ发送注册邮件或者其他推送邮件等，可以推送普通信息，学习例子

##使用方法说明
本例子使用php代码做示范，其他语言思想一致，使用composer组织代码。使用两个包：

composer.json中内容：
```javascript
"require": {
        "php-amqplib/php-amqplib": ">=2.6.3",
        "phpmailer/phpmailer": ">=5.2"
    }
```

添加这个是为了提高包下载速度:
```javascript
"repositories": {
        "packagist": {
            "type": "composer",
            "url": "https://packagist.phpcomposer.com"
        }
    }
```

如果你使用docker镜像RabbitMQ
```bash
# docker run --name littleRabbit -p 5672:5672 -d rabbitmq:alpine
```
使用步骤：
1.修改代码中的config.php中的参数为自己的邮箱参数
```php
$config = [
    'host' => 'smtp.emaildomain.com',
    'port' => 25,
    'username' => 'test@emaildomain.com',
    'userpass' => 'TheEmailPassword',
    'fromName' => 'From Test Demo',
    'fromAddr' => 'test@emaildomain.com'
];
```
2.启动
```bash
$ composer.phar install
$ php server.php &
$ php client.php
```

##其他
提示：例子是个抛砖引玉，应用于项目中需要改善。