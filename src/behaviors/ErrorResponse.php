<?php
/**
 * Error Response Behaviors
 */

namespace degree757\yii2s\behaviors;

use Yii;
use yii\base\Behavior;
use degree757\yii2s\components\ErrorHandle;

class ErrorResponse extends Behavior {
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
    
    public function events() {
        return [
            ErrorHandle::EVENT_BEFORE_RENDER => 'beforeRender'
        ];
    }
    
    public function beforeRender() {
        $exception = $this->owner->exception;
        
        $data['code']  = $exception->getCode();
        $data['msg']   = $exception->getMessage();
        $data['debug'] = $this->owner->getInfo();
        Yii::error($data);
        
        if (YII_ENV_PROD) {
            unset($data['debug']);
            $data['code'] = $this->prodCode;
            $data['msg']  = $this->prodMsg;
        }
        
        Yii::$app->response->data = $data;
        Yii::$app->response->send();
    }
}