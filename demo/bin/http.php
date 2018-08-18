<?php
/**
 * Start Script
 * Usage:
 *   php http.php start|reload|stop
 */
require(__DIR__ . '/../../vendor/autoload.php');

$http = require(__DIR__ . '/../config/http.php');
(new \degree757\yii2s\servers\Dispatcher($http))->run();