<?php

namespace app\components;

class Request extends \yii\web\Request {
    private $_swRequest;
    
    public function setSwRequest($request) {
        $this->_swRequest = $request;
    }
    
    public function getSwRequest() {
        return $this->_swRequest;
    }
    
    /**
     * 设置请求环境
     */
    public function setRequestEnv() {
        $this->getHeaders()->removeAll();
        $this->setQueryParams(null);
        $this->setBodyParams(null);

        $_SERVER['REQUEST_METHOD'] = $this->_swRequest->server['request_method'];
        foreach ($this->_swRequest->header as $name => $value) {
            $this->getHeaders()->set($name, $value);
        }
        $this->setQueryParams($this->_swRequest->get);
        $this->setRawBody($this->_swRequest->rawContent());
        
        $this->setPathInfo($this->_swRequest->server['path_info']);
    }
}
