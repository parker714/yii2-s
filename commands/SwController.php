<?php

namespace app\commands;

use Yii;
use yii\console\Controller;

class SwController extends Controller
{
    private $_http;

    public function actionRun()
    {
        $this->_http = new \swoole_http_server('0.0.0.0', 9501);
        $this->_http->on('start', [$this, 'onStart']);
        $this->_http->on('WorkerStart', [$this, 'onWorkerStart']);
        $this->_http->on('request', [$this, 'onRequest']);
        $this->_http->start();
    }

    public function onWorkerStart($server, $worker_id)
    {
        echo "sw worker is started,id is $worker_id \n";
    }

    public function onStart($server)
    {
        echo "sw http server is started at http://127.0.0.1:9501\n";
    }

    public function onRequest($request, $response)
    {
        $response->header("Content-Type", "application/json");

        $data = Yii::$app->getModule('user')->runAction($request->server['request_uri']);

        $response->end(json_encode($data));
    }
}