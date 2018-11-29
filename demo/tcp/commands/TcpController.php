<?php
/**
 * Yii2 Tcp Serve
 */

namespace app\commands;

use yii\console\Controller;

class TcpController extends Controller {
    private $_tcp;
    
    public function actionRun() {
        $this->_tcp = new \swoole_server('0.0.0.0', 18758);
        $this->_tcp->on('connect', [
            $this,
            'onConnect'
        ]);
        $this->_tcp->on('receive', [
            $this,
            'onReceive'
        ]);
        $this->_tcp->on('close', [
            $this,
            'onClose'
        ]);
        $this->_tcp->start();
    }
    
    public function onConnect($server, $fd) {
        $this->dump("$fd new connection");
    }
    
    public function onReceive($server, $fd, $reactor_id, $data) {
        //$server->send($fd, $data");
        $this->dump("receive data: $data");
    }
    
    public function onClose($server, $fd) {
        $this->dump("$fd connection close");
    }
    
    public function dump($msg) {
        echo sprintf("[%s]%s\n", date('Y-m-d H:i:s'), $msg);
    }
}
