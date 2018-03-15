<?php

$params = require __DIR__ . '/params.php';
$db     = require __DIR__ . '/db.php';

$config = [
    'id'                  => 'basic-console',
    'basePath'            => dirname(__DIR__),
    'bootstrap'           => ['log', 'gii'],
    'modules'             => [
        'gii'  => [
            'class' => 'yii\gii\Module'
        ],
        'user' => [
            'class' => 'app\modules\user\UserMod'
        ]
    ],
    'controllerNamespace' => 'app\commands',
    'aliases'             => [],
    'components'          => [
        'request' => [
            'class' => 'yii\console\Request',
        ],
        'session' => [
            'class' => 'yii\web\session'
        ],
        'cache'   => [
            'class' => 'yii\caching\FileCache',
        ],
        'log'     => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets'    => [
                [
                    'class'   => 'yii\log\FileTarget',
                    'levels'  => ['error', 'warning'],
                    'logVars' => []
                ],
            ],
        ],
        'db'      => $db,
    ],
    'params'              => $params,
];

return $config;
