<?php
/**
 * Sw Http Server Conf
 */
return ['worker_num'      => 2,
        'task_worker_num' => 1,
        'max_request'     => 1000,
        'dispatch_mode'   => 3,
        'daemonize'       => 0,
        'pid_file'        => __DIR__ . '/../bin/server.pid',
        'log_file'        => __DIR__ . '/../bin/sw.log'];