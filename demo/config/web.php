<?php
/**
 * Yii2 App Conf
 */

defined('YII_DEBUG') or define('YII_DEBUG', true);// true or false
defined('YII_ENV') or define('YII_ENV', 'dev');// dev or test or prod

return [
    'id'         => 757,
    'name'       => 'yii2-s',
    'basePath'   => dirname(__DIR__),
    'bootstrap'  => ['log'],
    'components' => [
        'errorHandler' => [
            'class' => \degree757\yii2s\components\ErrorHandle::class,
            'as errorHandler' => [
                'class' => \degree757\yii2s\behaviors\ErrorResponse::class,
                'prodCode' => 10000,
                'prodMsg' => 'system busy'
            ],
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
        'log'          => [
            'flushInterval' => 1,
            'targets'       => [
                [
                    'class'          => \yii\log\FileTarget::class,
                    'exportInterval' => 1,
                    'logVars'        => [],
                    'levels'         => ['error', 'warning', 'info']
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
