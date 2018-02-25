<?php

namespace app\commands;

use app\controllers\UserController;
use Yii;
use yii\base\Module;
use yii\console\Controller;

class SwooleController extends Controller
{
    private $_http;

    private $_webApp;

    private function _prepare()
    {
        $this->_http = new \swoole_http_server('0.0.0.0', 9501);
        $this->_http->on('start', [$this, 'onStart']);
        $this->_http->on('WorkerStart', [$this, 'onWorkerStart']);
        $this->_http->on('request', [$this, 'onRequest']);
    }

    public function actionStart()
    {
        $this->_prepare();
        $this->_http->start();
    }

    public function onWorkerStart($server, $worker_id){
        echo "Swoole worker is started,id is $worker_id \n";

        // 在这里运行web应用再解析
//        if($worker_id == 0){
//            $config = require(__DIR__ . '/../config/web.php');
//            $this->_webApp = new Application($config);
//        }
    }

    public function onStart($server)
    {
        echo "Swoole http server is started at http://127.0.0.1:9501\n";
    }

    public function onRequest($request, $response)
    {
        Yii::error("test log");
        $response->header("Content-Type", "application/json");
        $data = [
            'title'   => 'test',
            'server' => $request->server,
            'get'    => $request->get,
            'post'   => $request->post,
        ];

        // 设置请求参数
        Yii::$app->request->setQueryParams($request->get);
        Yii::$app->request->setQueryParams($request->post);

        // session
        //Yii::$app->session->set("name", "xx");
        $data['session_status'] = Yii::$app->session->isActive;
        //$data['session_values'] = Yii::$app->session->get('name');

        // db
        $data['user'] = UserController::ActionGet();

        // 请求路由分发
        $data['r'] = Yii::$app->runAction('hello/world');


        $response->end(json_encode($data));
    }
}