<?php
/**
 * Yii2-s Http Server Conf
 */
$server = include_once(__DIR__ . '/server.php');

return [
    'class'         => \degree757\yii2s\servers\Http::class,
    'processName'   => 'yii2-s',
    'ip'            => '0.0.0.0',
    'port'          => 18330,
    'set'           => $server,
    'workerStartCb' => function ($server, $workerId) {
        $appConf = require(__DIR__ . '/web.php');
        require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');
        
        new \degree757\yii2s\Application($appConf);
        Yii::$app->sw->setSwServer($server);
    },
];
