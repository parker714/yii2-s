<?php

namespace app\commands;

use yii\console\Controller;

class SwTcpController extends Controller
{
    private $_tcp;
    
    /**
     * run tcp server
     */
    public function actionRun()
    {
        $this->_tcp = new \swoole_server('0.0.0.0', 9503);
        $this->_tcp->on('connect', [$this, 'onConnect']);
        $this->_tcp->on('receive', [$this, 'onReceive']);
        $this->_tcp->on('close', [$this, 'onClose']);
        $this->_tcp->start();
    }
    
    public function onConnect($server, $fd)
    {
        echo "connection open: {$fd}\n";
    }
    
    public function onReceive($server, $fd, $reactor_id, $data)
    {
        $server->send($fd, "Swoole: {$data}");
        $server->close($fd);
    }
    
    public function onClose($server, $fd)
    {
        echo "connection close: {$fd}\n";
    }
}
