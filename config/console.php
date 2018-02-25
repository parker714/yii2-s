<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'aliases' => [
    ],
//    'as app'=>[
//        'class' => ''
//    ],
    'components' => [
        'request' => [
            'class'=>'yii\console\Request',
            'as request' => [
                'class' =>'app\Behaviors\Request'
            ]
        ],
        'session' =>[
            'class' => 'yii\web\session'
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'flushInterval' => 1,
            'targets' => [
                [
                    //'class' => 'yii\log\FileTarget',
                    'class' => 'app\components\Log',
                    'levels' => ['error', 'warning'],
                    'exportInterval' => 1,
                ],
            ],
        ],
        'db' => $db,
    ],
    'params' => $params,
    /*
    'controllerMap' => [
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
    ],
    */
];

return $config;
