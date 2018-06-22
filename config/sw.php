<?php
/**
 * sw conf
 */
return ['process_name'    => 'swoole-yii2',
        'ip'              => '0.0.0.0',
        'port'            => 18330,
        'worker_num'      => 2,
        'task_worker_num' => 1,
        'pid_file'        => __DIR__ . '/../bin/server.pid',
        //
        'max_request'     => 1,
        'dispatch_mode'   => 2,
        'daemonize'       => 0,
        'log_file'        => __DIR__ . '/../logs/sw.log'];