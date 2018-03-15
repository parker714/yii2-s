<?php

$params = require __DIR__ . '/params.php';
$db     = require __DIR__ . '/db.php';

$config = [
    'id'         => 'basic',
    'basePath'   => dirname(__DIR__),
    'bootstrap'  => ['log'],
    'aliases'    => [
    ],
    'modules'    => [
        'user' => [
            'class' => 'app\modules\user\UserMod'
        ]
    ],
    'components' => [
        'request'    => [
            'cookieValidationKey'  => 'xx',
            'enableCsrfValidation' => false
        ],
        'response'   => [
            'format' => 'json'
        ],
        'cache'      => [
            'class' => 'yii\caching\FileCache',
        ],
        'log'        => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets'    => [
                [
                    'class'   => 'yii\log\FileTarget',
                    'levels'  => ['error', 'warning'],
                    'logVars' => []
                ],
            ],
        ],
        'db'         => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName'  => false,
        ],
    ],
    'params'     => $params,
];

return $config;
