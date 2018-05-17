<?php
namespace app\components;

use yii\base\Component;

class Sw extends Component {
    public $_swServer;
    
    public function setSwServer($server){
        $this->_swServer = $server;
    }
    
    public function getSwServer(){
        return $this->_swServer;
    }
    
    /**
     * 执行异步
     * @param mixed ...$paramArr 与call_func_user()参数一致
     */
    public function task(...$paramArr){
        $this->_swServer->task($paramArr);
    }
}