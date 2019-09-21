<?php

namespace parker714\yii2s\servers;

use Yii;

/**
 * Class Tcp demo
 *
 * @package parker714\yii2s\servers
 */
class Tcp extends Server
{
    /**
     * Sw server process name
     *
     * @var string
     */
    public $processName = 'sw-tcp-server';

    /**
     * Sw tcp server events
     *
     * @var array
     */
    public $swEvents = ['Receive'];

    /**
     * Init tcp server
     *
     * @return mixed|\swoole_server
     */
    public function initSwServer()
    {
        return new \swoole_server($this->ip, $this->port);
    }

    /**
     * Sw tcp server receive callback event
     *
     * @param $server
     * @param $fd
     * @param $reactor_id
     * @param $data
     */
    public function onReceive($server, $fd, $reactor_id, $data)
    {
        Yii::info("receive data: {$data}");
    }
}