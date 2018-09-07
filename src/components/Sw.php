<?php
/**
 * Sw
 */
namespace degree757\yii2s\components;

use yii\base\Component;

class Sw extends Component {
    public $_swServer;
    
    public function setSwServer($server) {
        $this->_swServer = $server;
    }
    
    public function getSwServer(){
        return $this->_swServer;
    }
    
    /**
     * async func
     * @param mixed ...$paramArr, the same call_func_user() params
     */
    public function task(...$paramArr) {
        $this->_swServer->task($paramArr);
    }
}