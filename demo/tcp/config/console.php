<?php
/**
 * Yii2 Console App Conf
 */
return [
    'id'                  => 'tcp-serve',
    'basePath'            => dirname(__DIR__),
    'bootstrap'           => ['log'],
    'controllerNamespace' => 'app\commands',
    'components'          => [
        'log' => [
            'flushInterval' => 1,
            'targets'       => [
                [
                    'class'          => \yii\log\FileTarget::class,
                    'exportInterval' => 1,
                    'logVars'        => [],
                    'levels'         => [
                        'error',
                        'warning',
                        'info'
                    ]
                ],
            ],
        ],
    ],
];
