<?php
/**
 * sw http server
 */
namespace app\sw\server;

use app\sw\Application;

class Http {
    private $_http;
    private $_app;
    private $_swConf;
    private $_appConf;
    
    public function onStart($server) {
        echo "[Http Server {$this->_swConf['ip']}:{$this->_swConf['port']}] Start.\n";
        @swoole_set_process_name($this->_swConf['process_name']);
    }
    
    public function onWorkerStart($server, $workerId) {
        if($server->taskworker){
            echo "[Task Worker #{$workerId}] Start #{$workerId}.\n";
        }else{
            echo "[Worker #{$workerId}] Start #{$workerId}.\n";
        }
    
        @swoole_set_process_name($this->_swConf['process_name']);
        $this->_app = new Application($this->_appConf);
        $this->_app->sw->setSwServer($server);
    }
    
    public function onRequest($request, $response) {
        $this->_app->request->setSwRequest($request);
        $this->_app->response->setSwResponse($response);
        
        $this->_app->response->clear();
        $this->_app->request->setRequestEnv();
        $this->_app->run();
    }
    
    public function onTask($server, $taskId, $srcWorkerId, $data){
        try{
            call_user_func(...$data);
        }catch (\Exception $e){
            echo "[Task Worker #{$taskId}] Exception:\n";
            echo "[Task Worker #{$taskId}] file: {$e->getFile()}\n";
            echo "[Task Worker #{$taskId}] line: {$e->getLine()}\n";
            echo "[Task Worker #{$taskId}] msg:  {$e->getMessage()}\n";
        }
        $server->finish($data);
    }
    
    public function onFinish($server, $taskId, $data){
        echo "[Task Worker #{$taskId}] Finish.\n";
    }
    
    /**
     * 入口函数
     * @param $swConf
     * @param $appConf
     */
    public function run($swConf, $appConf) {
        $this->_swConf = $swConf;
        $this->_appConf = $appConf;
        
        $this->_http = new \swoole_http_server($this->_swConf['ip'], $this->_swConf['port']);
        $this->_http->on('start', [$this,'onStart']);
        $this->_http->on('WorkerStart', [$this,'onWorkerStart']);
        $this->_http->on('request', [$this,'onRequest']);
        $this->_http->on('task', [$this, 'onTask']);
        $this->_http->on('finish', [$this, 'onFinish']);
        $this->_http->set($this->_swConf);
        
        $this->_http->start();
    }
}