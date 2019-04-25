<?php

namespace parker714\yii2s\servers;

use Yii;
use yii\helpers\Console;

/**
 * Class Server
 * @package parker714\yii2s\servers
 */
abstract class Server
{
    /**
     * Sw server instance
     * @var Server
     */
    public $swServer;

    /**
     * Sw server events
     * @var array
     */
    public $swEvents = [];

    /**
     * Sw server process name
     * @var string
     */
    public $processName;

    /**
     * Sw server ip
     * @var string
     */
    public $ip = '0.0.0.0';

    /**
     * Sw server port
     * @var int
     */
    public $port = 10714;

    /**
     * Sw server config set, see: https://wiki.swoole.com/wiki/page/274.html
     * @var
     */
    public $set = [];

    /**
     * Start sw server
     * @throws \Exception
     */
    public function start()
    {
        @swoole_set_process_name($this->processName);

        $this->swServer = $this->initSwServer();
        $this->swServer->set($this->set);
        $this->bindEvents();

        $str = sprintf("%s | host:%s, port:%d, worker:%d " . PHP_EOL, $this->processName, $this->ip, $this->port, $this->set['worker_num']);
        Yii::$app->controller->stdout($str, Console::FG_RED);

        $this->swServer->start();
    }

    /**
     * Returns the coreSets
     * @return array
     */
    public function coreSets()
    {
        return [
            'worker_num'      => 2,
            'task_worker_num' => 1,
            'max_request'     => 1,
            'pid_file'        => '@app/server.pid',
            'log_file'        => '@runtime/sw.log',
        ];
    }

    /**
     * Init sw server
     * @return mixed
     */
    abstract public function initSwServer();

    /**
     * Bind sw callback events
     */
    public function bindEvents()
    {
        foreach ($this->swEvents as $event) {
            if (method_exists($this, 'on' . $event)) {
                $this->swServer->on($event, [
                    $this,
                    'on' . $event,
                ]);
            }
        }
    }

    /**
     * Returns the coreEvents
     * @return array
     */
    public function coreEvents()
    {
        return [
            'WorkerStart',
            'task',
            'finish',
        ];
    }

    /**
     * Get sw server pid file, use pid for server process control
     * @return mixed
     */
    public function getPidFile()
    {
        return $this->set['pid_file'];
    }

    /**
     * The sw work process starts the callback event
     * @param $server
     * @param $workerId
     */
    public function onWorkerStart($server, $workerId)
    {
        @swoole_set_process_name($this->processName);

        if ($server->taskworker) {
            Yii::info("Task Worker Start #{$workerId}");
        } else {
            Yii::info("Worker Start #{$workerId}");
        }
    }

    /**
     * The sw work task process starts the callback event, use sw components to accomplish async
     *
     * ```php
     * Yii::$app->sw->task('increment', $a);
     * ```
     *
     * @param $server
     * @param $taskId
     * @param $srcWorkerId
     * @param $data
     */
    public function onTask($server, $taskId, $srcWorkerId, $data)
    {
        try {
            call_user_func(...$data);
            $server->finish($data);
        } catch (\Exception $e) {
            $msg = "Task #{$taskId} srcWorker #{$srcWorkerId} Exception:\n";
            $msg .= "Err_data : {" . json_encode($data) . "}\n";
            $msg .= "Err_file : {$e->getFile()}\n";
            $msg .= "Err_line : {$e->getLine()}\n";
            $msg .= "Err_msg  : {$e->getMessage()}\n";
            $msg .= "Err_trace: {$e->getTraceAsString()}";
            Yii::error($msg);
        }
    }

    /**
     * The sw task process finish the callback event
     * @param $server
     * @param $taskId
     * @param $data
     */
    public function onFinish($server, $taskId, $data)
    {
        Yii::info("[Task #{$taskId}] Finish,data is " . json_encode($data));
    }
}