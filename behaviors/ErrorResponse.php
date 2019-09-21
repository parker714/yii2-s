<?php

namespace parker714\yii2s\behaviors;

use Yii;
use yii\base\Behavior;
use parker714\yii2s\components\ErrorHandle;

/**
 * Class ErrorResponse
 *
 * @package parker714\yii2s\behaviors
 */
class ErrorResponse extends Behavior
{
    /**
     * Prod env err code
     *
     * @var int
     */
    public $prodCode = 10000;

    /**
     * Prod env err msg
     *
     * @var string
     */
    public $prodMsg = 'system busy';

    /**
     * Behavior events
     *
     * @return array
     */
    public function events()
    {
        return [
            ErrorHandle::EVENT_AFTER_RENDER => 'afterRender',
        ];
    }

    /**
     * Api Error handle render
     */
    public function afterRender()
    {
        $exception = $this->owner->exception;

        $data['code']  = $exception->getCode();
        $data['msg']   = $exception->getMessage();
        $data['debug'] = self::getDebugInfo($exception);

        if (YII_ENV_PROD) {
            Yii::error($data);

            unset($data['debug']);
            $data['code'] = $this->prodCode;
            $data['msg']  = $this->prodMsg;
        }

        Yii::$app->response->data = $data;
        Yii::$app->response->send();
    }

    /**
     * Get error env info
     *
     * @param $exception
     *
     * @return array
     */
    public static function getDebugInfo($exception)
    {
        return [
            'request'     => Yii::$app->request->getInfo(),
            'error_code'  => $exception->getCode(),
            'error_file'  => $exception->getFile(),
            'error_line'  => $exception->getLine(),
            'error_msg'   => $exception->getMessage(),
            'error_trace' => explode(PHP_EOL, $exception->getTraceAsString()),
        ];
    }
}