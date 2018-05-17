<?php
/**
 * sw http server
 */
namespace app\sw\server;

class Http {
    /**
     * @var 应用实例
     */
    private $_app;
    /**
     * @var sw配置信息
     */
    private $_swConf;
    /**
     * @var sw http server
     */
    private $_http;
    
    public function onStart($server) {
        echo "server start".PHP_EOL;
        @swoole_set_process_name($this->_swConf['process_name']);
    }
    
    public function onWorkerStart($server, $workerId) {
        echo "server worker start #{$workerId}".PHP_EOL;
        $this->_app->sw->setSwServer($server);
    }
    
    public function onRequest($request, $response) {
        $this->setAppRunEnv($request, $response);
        $this->_app->run();
    }
    
    public function onTask($server, $taskId, $srcWorkerId, $data){
        try{
            call_user_func(...$data);
        }catch (\Exception $e){
            echo 'task error: '.$e->getMessage().PHP_EOL;
        }
        $server->finish($data);
    }
    
    public function onFinish($server, $taskId, $data){
        echo 'task finish.'.PHP_EOL;
    }
    
    /**
     * 入口函数
     * @param $swConf sw 配置
     * @param $app 应用实例
     */
    public function run($swConf, $app) {
        $this->_swConf = $swConf;
        $this->_app = $app;
        
        $this->_http = new \swoole_http_server($this->_swConf['ip'], $this->_swConf['port']);
        $this->_http->on('start', [$this,'onStart']);
        $this->_http->on('WorkerStart', [$this,'onWorkerStart']);
        $this->_http->on('request', [$this,'onRequest']);
        $this->_http->on('task', [$this, 'onTask']);
        $this->_http->on('finish', [$this, 'onFinish']);
        $this->_http->set($this->_swConf['server']);
        
        $this->_http->start();
    }
    
    /**
     * 设置应用环境
     * @param $request  sw request对象
     * @param $response sw response对象
     */
    public function setAppRunEnv($request, $response) {
        $this->_app->request->setSwRequest($request);
        $this->_app->response->setSwResponse($response);
        
        $this->_app->response->clear();
        $this->_app->request->setRequestEnv();
    }
}