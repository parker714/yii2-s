<?php
/**
 * sw http conf
 */
return ['process_name' => 'sw_yii2',
        'ip'           => '0.0.0.0',
        'port'         => 9501,
        'server'       => ['pid_file'        => __DIR__ . '/server.pid',
                           'worker_num'      => 1,
                           'task_worker_num' => 1,
                           'max_request'     => 500,
                           'dispatch_mode'   => 2,
                           'daemonize'       => 0]];