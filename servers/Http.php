<?php

namespace degree757\yii2s\servers;

use Yii;

/**
 * Class Http
 * @package degree757\yii2s\servers
 */
class Http extends Server
{
    /**
     * sw http server events
     *
     * @var array
     */
    public $swEvents = ['WorkerStart',
                        'task',
                        'finish',
                        'Request'];

    /**
     * Yii2 web app config
     * @var array
     */
    public $webAppConf = [];

    public function getSwServer()
    {
        return new \swoole_http_server($this->ip, $this->port);
    }

    /**
     * The sw http server work process starts the callback event
     * @param $server
     * @param $workerId
     * @throws \yii\base\InvalidConfigException
     */
    public function onWorkerStart($server, $workerId)
    {
        parent::onWorkerStart($server, $workerId);

        new \degree757\yii2s\Application($this->webAppConf);
    }

    /**
     * Sw http server request callback event
     * @param $request
     * @param $response
     */
    public function onRequest($request, $response)
    {
        $this->setAppRunEnv($request, $response);
        Yii::$app->run();
    }

    /**
     * Set yii2 app run env
     * @param $request
     * @param $response
     */
    public function setAppRunEnv($request, $response)
    {
        Yii::$app->request->setSwRequest($request);
        Yii::$app->response->setSwResponse($response);

        Yii::$app->request->getHeaders()
            ->removeAll();
        Yii::$app->response->clear();

        foreach ($request->server as $k => $v) {
            $_SERVER[strtoupper($k)] = $v;
        }
        Yii::$app->request->setPathInfo($request->server['path_info']);
        foreach ($request->header as $name => $value) {
            Yii::$app->request->getHeaders()
                ->set($name, $value);
        }
        Yii::$app->request->setQueryParams($request->get);
        Yii::$app->request->setBodyParams($request->post);

        $rawContent = $request->rawContent() ?: null;
        Yii::$app->request->setRawBody($rawContent);
    }
}