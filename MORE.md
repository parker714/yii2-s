### 1.Customize the sw server configuration、processName、ip 、port
```console.php
'controllerMap' => [
    'sw-http' => [
        'class'  => \parker714\yii2s\SwController::class,
        'server' => [
            'class'      => \parker714\yii2s\servers\Http::class,
            'webAppConf' => require(__DIR__ . '/web.php'),
            
            'processName'=> 'sw-http-server',
            'ip'         => '127.0.0.1',
            'port'       => 10714,
            'set'        => [
                'worker_num'      => 2,
                'task_worker_num' => 1,
                'daemonize'       => 0,
                // Support yii2 aliases
                'pid_file'        => '@app/server.pid',
                'log_file'        => '@runtime/sw.log',
            ],
        ],
    ],
]
```
### 2.Usage Tcp Server
```console.php
'bootstrap'     => ['log'],
'controllerMap' => [
    'sw-tcp' => [
        'class' => \parker714\yii2s\SwController::class,
        'server' => [
            // Or use a subclass that inherits the class
            'class' => \parker714\yii2s\servers\Tcp::class,
        ],
    ]
],    
```

### 3.Optional component to retrieve the sw original server object
```web.php
'components' => [
    ...
    'sw' => [
        'class' => \parker714\yii2s\components\Sw::class,
    ],
],

Example: 
    1.Usage async by sw, task params the same call_func_user() params
    Yii::$app->sw->task(['app\controllers\UserController','sendEmail',], 1, "hello");
```