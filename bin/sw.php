<?php

defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'prod');

// 注册 Composer 自动加载器
require(__DIR__ . '/../vendor/autoload.php');

// 包含 Yii 类文件
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

class Swoole
{
    private $_http;

    private $_yii2WebApp;

    private function _prepare()
    {
        $this->_http = new \swoole_http_server('0.0.0.0', 9501);
        $this->_http->on('start', [$this, 'onStart']);
        $this->_http->on('WorkerStart', [$this, 'onWorkerStart']);
        $this->_http->on('request', [$this, 'onRequest']);

        $this->_http->set(
            [
                'worker_num'    => 4,    //worker process num
                'max_request'   => 1000,
                'dispatch_mode' => 2,

                'daemonize' => 0,// 后台运行
                //'log_file' =>'/media/sf_www/swoole/yii2/sw.log',// 日志文件地址
            ]
        );
    }

    public function actionStart()
    {
        $this->_prepare();
        $this->_http->start();
    }

    public function onWorkerStart($server, $worker_id)
    {
        echo "Swoole worker is started,id is $worker_id \n";

        $config            = require(__DIR__ . '/../config/web.php');
        $this->_yii2WebApp = new \yii\web\Application($config);
    }

    public function onStart($server)
    {
        echo "Swoole http server is started at http://127.0.0.1:9501\n";
    }

    public function onRequest($request, $response)
    {
        $response->header("Content-Type", "application/json");

        // 请求路由分发
        $this->_yii2WebApp->request->setPathInfo($request->server['request_uri']);
        $yii2WebResponse = $this->_yii2WebApp->handleRequest($this->_yii2WebApp->request);

        $response->end(json_encode($yii2WebResponse->data));
    }
}

(new Swoole())->actionStart();