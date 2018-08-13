<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');

$swConf = require(__DIR__ . '/../config/server.php');
$appConf = require(__DIR__ . '/../config/web.php');

(new \degree757\yii2s\servers\Http($swConf, $appConf))->run();