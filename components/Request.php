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
    
    public function setRequestEnv() {
        $this->getHeaders()->removeAll();
        foreach ($this->_swRequest->header as $name => $value) {
            $this->getHeaders()->add($name, $value);
        }
        
        $_GET                      = isset($this->_swRequest->get) ? $this->_swRequest->get : [];
        $_POST                     = isset($this->_swRequest->post) ? $this->_swRequest->post : [];
        $_SERVER['REQUEST_METHOD'] = $this->_swRequest->server['request_method'];
        
        $this->setBodyParams(null);
        $this->setRawBody($this->_swRequest->rawContent());
        
        $this->setPathInfo($this->_swRequest->server['path_info']);
    }
}
