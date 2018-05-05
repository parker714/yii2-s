<?php
namespace app\components;

use yii\base\Component;

class Sw extends Component {
    public $_swServer;
    
    public function setSwServer($server){
        $this->_swServer = $server;
    }
    
    public function task($callback , $paramArr){
        $this->_swServer->task([$callback , $paramArr]);
    }
}