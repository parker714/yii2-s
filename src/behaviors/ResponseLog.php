<?php
/**
 * Response log
 */

namespace degree757\yii2s\behaviors;

use Yii;
use yii\base\Behavior;
use degree757\yii2s\components\Response;

class ResponseLog extends Behavior {
    public function events() {
        return [
            Response::EVENT_BEFORE_SEND => 'beforeSend'
        ];
    }
    
    public function beforeSend() {
        $log = [
            'req'  => Yii::$app->request->getInfo(),
            'resp' => $this->owner->data
        ];
        Yii::info(json_encode($log));
    }
}