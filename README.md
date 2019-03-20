<p align="center">
    <h1 align="center">Swoole Extension for Yii 2</h1>
</p>

This extension provides an swoole for [Yii framework 2.0](http://www.yiiframework.com) based on [swoole](https://www.swoole.com/).

For license information check the [LICENSE](LICENSE.md)-file.

[![Latest Stable Version](https://poser.pugx.org/degree757/yii2-s/v/stable.png)](https://packagist.org/packages/degree757/yii2-s)
[![Total Downloads](https://poser.pugx.org/degree757/yii2-s/downloads.png)](https://packagist.org/packages/degree757/yii2-s)


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

    composer require degree757/yii2-s

Usage
-----

After installation, you need to modify the configuration and then use it:

```
# 1.Add the configuration in the controllerMap (console.php)
...
'controllerMap' => [
    'sw-http' => [
        'class'  => \degree757\yii2s\SwController::class,
        'server' => [
            'class'      => \degree757\yii2s\servers\Http::class,
            'webAppConf' => require(__DIR__ . '/web.php'),
            
        //  Customize the server configuration
        //  'set'        => [
        //      'worker_num'      => 2,
        //      'task_worker_num' => 1,
        //      'daemonize'       => 0,
        //      'pid_file'        => '@app/server.pid',
        //      'log_file'        => '@runtime/sw.log',
        //  ],
        ],
    ],
    'sw-tcp' => [
        'class' => \degree757\yii2s\SwController::class,
        'server' => [
            // Or use a subclass that inherits the class
            'class' => \degree757\yii2s\servers\Tcp::class,
        ],
    ]
...        

# 2.Management server
./yii sw-http/server start|stop|reload
```


Add WeChat to learn more
----------

![Usage example of Yii2 shell](pb.jpeg)
