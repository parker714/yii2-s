<?php
namespace app\sw\server;

class Http {
    private $_app;
    private $_http;
    
    public function run($conf, $app) {
        $this->_app = $app;
        $this->_http = new \swoole_http_server('0.0.0.0', 9501);
        $this->_http->on('start', [$this,'onStart']);
        $this->_http->on('WorkerStart', [$this,'onWorkerStart']);
        $this->_http->on('request', [$this,'onRequest']);
        $this->_http->on('task', [$this, 'onTask']);
        $this->_http->on('finish', [$this, 'onFinish']);
        $this->_http->set($conf);
        
        $this->_http->start();
    }
    
    public function onStart($server) {
        echo "server start".PHP_EOL;
        $this->_app->sw->setSwServer($server);
    }
    
    public function onWorkerStart($server, $worker_id) {
        echo "server worker start #{$worker_id}".PHP_EOL;
    }
    
    public function onRequest($request, $response) {
        $this->setAppRunEnv($request, $response);
        $this->_app->run();
    }
    
    public function onTask($server, $task_id, $src_worker_id, $data){
        try{
            call_user_func_array($data[0],$data[1]);
        }catch (\Exception $e){
            echo 'task error: '.$e->getMessage().PHP_EOL;
        }
        $server->finish($data);
    }
    
    public function onFinish($server, $task_id, $data){
        echo 'task finish:'.PHP_EOL;
    }
    
    public function setAppRunEnv($request, $response) {
        $this->_app->response->clear();
        $this->_app->request->setSwRequest($request);
        $this->_app->response->setSwResponse($response);
        $this->_app->request->setRequestEnv();
    }
}