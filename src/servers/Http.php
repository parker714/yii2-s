<?php
namespace degree757\yii2s\servers;

use Yii;
use degree757\yii2s\Application;

class Http {
    private $_http;
    private $_swConf;
    private $_appConf;
    
    public function onStart($server) {
        @swoole_set_process_name($this->_swConf['process_name']);
        echo "[Http Server {$this->_swConf['ip']}:{$this->_swConf['port']}] Start.\n";
    }
    
    public function onWorkerStart($server, $workerId) {
        @swoole_set_process_name($this->_swConf['process_name']);
        
        if($server->taskworker){
            echo "[Task Worker #{$workerId}] Start #{$workerId}.\n";
        }else{
            echo "[Worker #{$workerId}] Start #{$workerId}.\n";
        }
        
        new Application($this->_appConf);
        Yii::$app->sw->setSwServer($server);
    }
    
    public function onRequest($request, $response) {
        $this->setAppRunEnv($request, $response);
        Yii::$app->run();
    }
    
    public function onTask($server, $taskId, $srcWorkerId, $data){
        try{
            call_user_func(...$data);
            $server->finish($data);
        }catch (\Exception $e){
            echo "[Task Worker #{$taskId}] Exception:\n";
            echo "[Task Worker #{$taskId}] err_file: {$e->getFile()}\n";
            echo "[Task Worker #{$taskId}] err_line: {$e->getLine()}\n";
            echo "[Task Worker #{$taskId}] err_msg:  {$e->getMessage()}\n";
        }
    }
    
    public function onFinish($server, $taskId, $data){
        echo "[Task Worker #{$taskId}] Finish.\n";
    }
    
    /**
     * Http constructor.
     *
     * @param $swConf
     * @param $appConf
     */
    public function __construct($swConf, $appConf) {
        $this->_swConf = $swConf;
        $this->_appConf = $appConf;
    }
    
    /**
     * Main func
     */
    public function run() {
        $this->_http = new \swoole_http_server($this->_swConf['ip'], $this->_swConf['port']);
        $this->_http->on('start', [$this,'onStart']);
        $this->_http->on('WorkerStart', [$this,'onWorkerStart']);
        $this->_http->on('request', [$this,'onRequest']);
        $this->_http->on('task', [$this, 'onTask']);
        $this->_http->on('finish', [$this, 'onFinish']);
        $this->_http->set($this->_swConf);
        $this->_http->start();
    }
    
    /**
     * Set app run env
     *
     * @param $request
     * @param $response
     */
    public function setAppRunEnv($request, $response){
        Yii::$app->request->setSwRequest($request);
        Yii::$app->response->setSwResponse($response);
    
        Yii::$app->request->getHeaders()->removeAll();
        Yii::$app->response->clear();
        
        foreach ($request->server as $k => $v) {
            $_SERVER[strtoupper($k)] = $v;
        }
        Yii::$app->request->setPathInfo($request->server['path_info']);
        foreach ($request->header as $name => $value) {
            Yii::$app->request->getHeaders()->set($name, $value);
        }
        Yii::$app->request->setQueryParams($request->get);
        Yii::$app->request->setBodyParams($request->post);
        Yii::$app->request->setRawBody($request->rawContent());
    }
}