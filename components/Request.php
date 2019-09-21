<?php

namespace parker714\yii2s\components;

/**
 * Class Request
 *
 * @package parker714\yii2s\components
 */
class Request extends \yii\web\Request
{
    /**
     * @var \swoole_http_request
     */
    public $swRequest;

    /**
     * get request info
     *
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getInfo()
    {
        return ['path_info' => $this->getPathInfo(),
                'method'    => $this->getMethod(),
                'header'    => $this->getHeaders()
                    ->toArray(),
                'get'       => $this->getQueryParams(),
                'post'      => $this->getBodyParams()];
    }
}