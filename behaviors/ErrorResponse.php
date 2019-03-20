<?php

namespace degree757\yii2s\behaviors;

use Yii;
use yii\base\Behavior;
use degree757\yii2s\components\ErrorHandle;

/**
 * Class ErrorResponse
 * @package degree757\yii2s\behaviors
 */
class ErrorResponse extends Behavior
{
    /**
     * prod err code
     *
     * @var int
     */
    public $prodCode = 10000;
    /**
     * prod err msg
     *
     * @var string
     */
    public $prodMsg = 'system busy';

    public function events()
    {
        return [
            ErrorHandle::EVENT_AFTER_RENDER => 'afterRender',
        ];
    }

    public function afterRender()
    {
        $exception = $this->owner->exception;

        $data['code']  = $exception->getCode();
        $data['msg']   = $exception->getMessage();
        $data['debug'] = self::getDebugInfo($exception);
        Yii::error($data);

        if (YII_ENV_PROD) {
            unset($data['debug']);
            $data['code'] = $this->prodCode;
            $data['msg']  = $this->prodMsg;
        }

        Yii::$app->response->data = $data;
        Yii::$app->response->send();
    }

    /**
     * Get error request env info
     * @param $exception
     *
     * @return array
     */
    public static function getDebugInfo($exception)
    {
        return [
            'request_info' => Yii::$app->request->getInfo(),
            'error_code'   => $exception->getCode(),
            'error_file'   => $exception->getFile(),
            'error_line'   => $exception->getLine(),
            'error_msg'    => $exception->getMessage(),
            'error_trace'  => explode(PHP_EOL, $exception->getTraceAsString()),
        ];
    }
}