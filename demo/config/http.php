<?php
/**
 * yii2-s Http Server Conf
 */

return [
    'class'         => \degree757\yii2s\servers\Http::class,
    'processName'   => 'yii2-s',
    'ip'            => '0.0.0.0',
    'port'          => 18757,
    'set'           => [
        'worker_num'      => 1,
        'task_worker_num' => 1,
        'max_request'     => 1000,
        'dispatch_mode'   => 3,
        'daemonize'       => 0,
        'pid_file'        => __DIR__ . '/../bin/server.pid',
        'log_file'        => __DIR__ . '/../bin/sw.log'
    ],
    'workerStartCb' => function ($server, $workerId) {
        $appConf = require(__DIR__ . '/web.php');
        require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');
        
        new \degree757\yii2s\Application($appConf);
        Yii::$app->sw->setSwServer($server);
    },
];
