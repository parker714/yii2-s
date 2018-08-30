<?php
/**
 * Sw Http Server
 */

namespace degree757\yii2s\servers;

use Yii;

class Http extends Server {
    /**
     * sw Http events
     *
     * @var array
     */
    public $swEvents = ['Request'];
    
    public function getSwServer() {
        return new \swoole_http_server($this->ip, $this->port);
    }
    
    public function onWorkerStart($server, $workerId) {
        parent::onWorkerStart($server, $workerId);
        call_user_func($this->workerStartCb, $server, $workerId);
    }
    
    public function onRequest($request, $response) {
        $this->setAppRunEnv($request, $response);
        Yii::$app->run();
    }
    
    /**
     * Set Yii2 App Run Env
     * @param $request
     * @param $response
     */
    public function setAppRunEnv($request, $response) {
        Yii::$app->request->setSwRequest($request);
        Yii::$app->response->setSwResponse($response);
        
        Yii::$app->request->getHeaders()
                          ->removeAll();
        Yii::$app->response->clear();
        
        foreach ($request->server as $k => $v) {
            $_SERVER[strtoupper($k)] = $v;
        }
        Yii::$app->request->setPathInfo($request->server['path_info']);
        foreach ($request->header as $name => $value) {
            Yii::$app->request->getHeaders()
                              ->set($name, $value);
        }
        Yii::$app->request->setQueryParams($request->get);
        Yii::$app->request->setBodyParams($request->post);
        
        $rawContent = $request->rawContent() ?: null;
        Yii::$app->request->setRawBody($rawContent);
    }
}