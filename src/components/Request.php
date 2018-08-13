<?php

namespace degree757\yii2s\components;

class Request extends \yii\web\Request {
    private $_swRequest;
    
    public function setSwRequest($request) {
        $this->_swRequest = $request;
    }
    
    public function getSwRequest() {
        return $this->_swRequest;
    }
    
    public function getInfo() {
        return ['path_info' => $this->getPathInfo(),
                'method'    => $this->getMethod(),
                'header'    => $this->getHeaders()
                                    ->toArray(),
                'get'       => $this->getQueryParams(),
                'post'      => $this->getBodyParams()];
    }
}