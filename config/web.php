<?php

$params = require __DIR__ . '/params.php';
$db     = require __DIR__ . '/db.php';

$config = [
    'id'         => 'basic',
    'basePath'   => dirname(__DIR__),
    'bootstrap'  => ['log'],
    'components' => [
        'request'    => [
            'class'  => \app\components\Request::class,
            'cookieValidationKey'  => 'pb',
            'enableCsrfValidation' => false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'errorHandler' => [
            'class'  => \app\components\ErrorHandler::class,
        ],
        'response'   => [
            'class'  => \app\components\Response::class,
            'format' => \yii\web\Response::FORMAT_JSON,
        ],
        'cache'      => [
            'class' => 'yii\caching\FileCache',
        ],
        'log'        => [
            'targets' => [
                [
                    'class'   => 'yii\log\FileTarget',
                    'levels'  => ['error', 'warning'],
                    'logVars' => []
                ],
            ],
        ],
        'db'         => $db,
//        'urlManager' => [
//            'enablePrettyUrl'     => true,
//            'enableStrictParsing' => false
//        ],
        'urlManager' => [
            'enablePrettyUrl'     => true,
            'enableStrictParsing' => true,
            'showScriptName'      => false,
            'rules'               => [
                ['class' => 'yii\rest\UrlRule', 'controller' => 'user'],
            ],
        ]
    ],
    'params'     => $params,
];

return $config;
