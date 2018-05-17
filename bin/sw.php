<?php

require('/Users/pb/Work/xhprof/prepend.php');

defined('YII_DEBUG') or define('YII_DEBUG', true); // true or false
defined('YII_ENV') or define('YII_ENV', 'dev'); // dev or prod

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../sw/Application.php');
require(__DIR__ . '/../sw/server/Http.php');

//----------------- yii2 application --------
$conf = include(__DIR__ . '/../config/web.php');
$app  = new \app\sw\Application($conf);

//---------------- sw application ----------------
$swConf = include(__DIR__ . '/../config/sw.php');
(new \app\sw\server\Http())->run($swConf, $app);