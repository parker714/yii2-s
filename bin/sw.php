<?php
//----------------- yii2 web application --------
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../sw/Application.php');

$conf = include(__DIR__ . '/../config/web.php');
$app  = new \app\sw\Application($conf);

//---------------- sw application ----------------
$swConf = ['pid_file'      => __DIR__ . '/server.pid',
           'worker_num'    => 1,
           'max_request'   => 500,
           'dispatch_mode' => 2,
           'daemonize'     => 0];

require(__DIR__ . '/../sw/server/Http.php');
(new \app\sw\server\Http())->run($swConf, $app);