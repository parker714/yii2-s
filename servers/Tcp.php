<?php

namespace parker714\yii2s\servers;

/**
 * Class Tcp demo
 * @package parker714\yii2s\servers
 */
class Tcp extends Server
{
    /**
     * Sw server process name
     * @var string
     */
    public $processName = 'sw-tcp-server';

    /**
     * Sw tcp server events
     * @var array
     */
    public $swEvents = ['WorkerStart',
                        'task',
                        'finish',
                        'receive'];

    public function getSwServer()
    {
        return new \swoole_server($this->ip, $this->port);
    }

    /**
     * Sw tcp server receive callback event
     * @param $server
     * @param $fd
     * @param $reactor_id
     * @param $data
     */
    public function onReceive($server, $fd, $reactor_id, $data)
    {
        echo "receive data: {$data}\n";
    }
}