<?php

namespace parker714\yii2s\components;

use yii\base\Component;

/**
 * Class Sw
 *
 * @package parker714\yii2s\components
 */
class Sw extends Component
{
    /**
     * @var \swoole_server
     */
    public $server;

    /**
     * async func
     *
     * @param mixed ...$paramArr , the same call_func_user() params
     */
    public function task(...$paramArr)
    {
        $this->server->task($paramArr);
    }
}