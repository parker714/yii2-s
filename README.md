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
# 1.Add the configuration in the controllerMap (console.php)
...
'bootstrap'     => ['log'],
'controllerMap' => [
    'sw-http' => [
        // Sw service scheduling
        'class'  => \parker714\yii2s\SwController::class,
        'server' => [
            // Sw tcp、http service implementation
            'class'      => \parker714\yii2s\servers\Http::class,
            // Yii2 restful app conf
            'webAppConf' => require(__DIR__ . '/web.php'),
            
        //  Customize the sw server configuration
        //  'set'        => [
        //      'worker_num'      => 2,
        //      'task_worker_num' => 1,
        //      'daemonize'       => 0,
        //      // Support yii2 aliases
        //      'pid_file'        => '@app/server.pid',
        //      'log_file'        => '@runtime/sw.log',
        //  ],
        ],
    ],
    // Another example
    'sw-tcp' => [
        'class' => \parker714\yii2s\SwController::class,
        'server' => [
            // Or use a subclass that inherits the class
            'class' => \parker714\yii2s\servers\Tcp::class,
        ],
    ]
...  

# About yii2 restful app conf，sw exception、request、response are different from php-fpm and need to be rewritten
...
'components' => [
    'errorHandler' => [
        'class'            => \parker714\yii2s\components\ErrorHandle::class,
        // Handle interface exceptions using events
        'as errorResponse' => [
            'class'    => \parker714\yii2s\behaviors\ErrorResponse::class,
            'prodCode' => 10000,
            'prodMsg'  => 'system busy',
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
    ],
    // Optional component to retrieve the sw original server object
    'sw'           => [
        'class' => \parker714\yii2s\components\Sw::class,
    ],
...

# 2.Management server
./yii sw-http/server start|stop|reload

# 3.View server
http://127.0.0.1:18757
```


Add WeChat to learn more
----------

![Usage example of Yii2 shell](pb.jpeg)
