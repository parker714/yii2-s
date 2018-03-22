<?php

class swHttp {
    private $_http;
    
    public function run($conf) {
        $this->_http = new \swoole_http_server('0.0.0.0', 9501);
        $this->_http->on('start', [$this,
                                   'onStart']);
        $this->_http->on('WorkerStart', [$this,
                                         'onWorkerStart']);
        $this->_http->on('request', [$this,
                                     'onRequest']);
        $this->_http->set($conf);
        
        $this->_http->start();
    }
    
    public function onStart($server) {
    }
    
    public function onWorkerStart($server, $worker_id) {
        $config = include(__DIR__ . '/../config/web.php');
        new \app\sw\Application($config);
    }
    
    public function onRequest($request, $response) {
        $this->setYii2Env($request, $response);
        Yii::$app->run();
    }
    
    public function setYii2Env($request, $response) {
        Yii::$app->response->clear();
        
        Yii::$app->request->setSwRequest($request);
        Yii::$app->response->setSwResponse($response);
        
        Yii::$app->request->setRequestEnv();
    }
}

//----------------- yii2 wen application--------
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../sw/Application.php');

//----------------run sw------------------------
$swConf = ['pid_file'      => __DIR__ . '/server.pid',
           'worker_num'    => 4,
           // worker process num
           'max_request'   => 1000,
           'dispatch_mode' => 2,
           'daemonize'     => 0,
           // 后台运行
];
(new swHttp())->run($swConf);