<?php

return ['process_name'    => 'yii2-s',
        'ip'              => '0.0.0.0',
        'port'            => 18330,
        'worker_num'      => 1,
        'task_worker_num' => 1,
        'max_request'     => 1,
        'dispatch_mode'   => 1,
        'daemonize'       => 0,
        'pid_file'        => __DIR__ . '/../bin/server.pid',
        'log_file'        => __DIR__ . '/../bin/sw.log'];