<?php

$config = [
    'id'         => 20000,
    'name'       => 'yii2-s',
    'basePath'   => dirname(__DIR__),
    'bootstrap'  => ['log'],
    'components' => [
        'errorHandler' => [
            'class' => \degree757\yii2s\components\ErrorHandle::class,
        ],
        'request'      => [
            'class'                => \degree757\yii2s\components\Request::class,
            'cookieValidationKey'  => 'pb!@#$%^&*()',
            'enableCsrfValidation' => false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser'
            ]
        ],
        'response'     => [
            'class'       => \degree757\yii2s\components\Response::class,
            'format'      => \yii\web\Response::FORMAT_JSON
        ],
        'sw'      => [
            'class' => \degree757\yii2s\components\Sw::class
        ],
        'cache'        => [
            'class' => 'yii\caching\FileCache',
        ],
        'log'          => [
            'flushInterval' => 1,
            'targets'       => [
                [
                    'class'          => \yii\log\FileTarget::class,
                    'exportInterval' => 1
                ]
            ],
        ],
        'urlManager'   => [
            'enablePrettyUrl'     => true,
            'enableStrictParsing' => true,
            'showScriptName'      => false,
            'rules'               => [
                [
                    'class'         => \yii\rest\UrlRule::class,
                    'pluralize'     => false,
                    'controller'    => ['user']
                ]
            ],
        ],
    ]
];

return $config;
