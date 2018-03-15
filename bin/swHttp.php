<?php

class swHttp
{
    private $_http;

    private $_yii2App;

    public function run($conf, $app)
    {
        $this->_http = new \swoole_http_server('0.0.0.0', 9501);
        $this->_http->on('start', [$this, 'onStart']);
        $this->_http->on('WorkerStart', [$this, 'onWorkerStart']);
        $this->_http->on('request', [$this, 'onRequest']);
        $this->_http->set($conf);

        $this->_yii2App = $app;

        $this->_http->start();
    }

    public function onWorkerStart($server, $worker_id)
    {
        echo "sw worker is started,id is $worker_id " . PHP_EOL;
    }

    public function onStart($server)
    {
        echo "sw http server is started at http://127.0.0.1:9501 " . PHP_EOL;
    }

    public function onRequest($request, $response)
    {
        $response->header("Content-Type", "application/json");

        $data = $this->_yii2App->getModule('user')->runAction($request->server['request_uri']);

        $response->end(json_encode($data));
    }
}

// yii2 wen application
defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'prod');
require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');
$config = include(__DIR__ . '/../config/web.php');
$app    = new \yii\web\Application($config);

$swConf = [
    'pid_file'      => __DIR__ . '/server.pid',
    'worker_num'    => 4,    // worker process num
    'max_request'   => 1000,
    'dispatch_mode' => 2,
    'daemonize'     => 0,// 后台运行
];

// run
(new swHttp())->run($swConf, $app);