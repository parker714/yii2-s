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
        $this->_http->set($conf);
        
        $this->_http->start();
    }
    
    public function onStart($server) {
    }
    
    public function onWorkerStart($server, $worker_id) {
    }
    
    public function onRequest($request, $response) {
        $this->setAppRunEnv($request, $response);
        $this->_app->run();
    }
    
    public function setAppRunEnv($request, $response) {
        $this->_app->response->clear();
        $this->_app->request->setSwRequest($request);
        $this->_app->response->setSwResponse($response);
        $this->_app->request->setRequestEnv();
    }
}