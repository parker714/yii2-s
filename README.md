<p align="center">
    <h1 align="center">Swoole Extension for Yii 2</h1>
</p>

This extension provides an swoole for [Yii framework 2.0](http://www.yiiframework.com) based on [swoole](https://www.swoole.com/).

For license information check the [LICENSE](LICENSE.md)-file.

[![Latest Stable Version](https://poser.pugx.org/parker714/yii2-s/v/stable.png)](https://packagist.org/packages/parker714/yii2-s)


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

    composer require parker714/yii2-s -vvv

Usage
-----

After installation, you need to modify the configuration and then use it:

```
# 1.Add the configuration in the console.php
...
'bootstrap'     => ['log'],
'controllerMap' => [
    'sw-http' => [
        'class'  => \parker714\yii2s\SwController::class,
        'server' => [
            'class'      => \parker714\yii2s\servers\Http::class,
            'webAppConf' => require(__DIR__ . '/web.php'),
    ],
...  

# 2.Put the web.php configuration，sw exception、request、response are different from php-fpm
...
'components' => [
    'errorHandler' => [
        'class'            => \parker714\yii2s\components\ErrorHandle::class,
        'as errorResponse' => [
            'class'    => \parker714\yii2s\behaviors\ErrorResponse::class,
        ],
    ],
    'response'     => [
        'class'  => \parker714\yii2s\components\Response::class,
        'format' => \yii\web\Response::FORMAT_JSON,
    ],
    'request'      => [
        'class'   => \parker714\yii2s\components\Request::class,
        'parsers' => [
            'application/json' => \yii\web\JsonParser::class,
        ],
    ]
...

# 3.Management server
./yii sw-http/server start|stop|reload
```

Tutorials
----------
 1.[More ways to use](MORE.md)
 
 2.[The idea of using swoole in yii2](https://www.jianshu.com/p/9c2788ccf3c0)

Add WeChat to learn more
----------

![Usage example of Yii2 shell](pb.jpeg)
