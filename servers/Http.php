<?php

namespace parker714\yii2s\servers;

use Yii;
use parker714\yii2s\Application;

/**
 * Class Http
 *
 * @package parker714\yii2s\servers
 */
class Http extends Server
{
    /**
     * Sw server process name
     *
     * @var string
     */
    public $processName = 'sw-http-server';

    /**
     * sw http server events
     *
     * @var array
     */
    public $swEvents = ['Request'];

    /**
     * Yii2 web app config
     *
     * @var array
     */
    public $webAppConf = [];

    /**
     * Init http server
     *
     * @return mixed|\swoole_http_server
     */
    public function initSwServer()
    {
        return new \swoole_http_server($this->ip, $this->port);
    }

    /**
     * The sw http server work process starts the callback event
     *
     * @param $server
     * @param $workerId
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function onWorkerStart($server, $workerId)
    {
        parent::onWorkerStart($server, $workerId);

        new Application($this->webAppConf);

        // Save sw server in yii2 components，Convenient use of the sw server method
        if (Yii::$app->has('sw')) {
            Yii::$app->sw->server = $server;
        }
    }

    /**
     * Sw http server request callback event
     *
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
     *
     * @param $request
     * @param $response
     */
    public function setAppRunEnv($request, $response)
    {
        Yii::$app->request->swRequest   = $request;
        Yii::$app->response->swResponse = $response;

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