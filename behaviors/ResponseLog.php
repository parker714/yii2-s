<?php

namespace parker714\yii2s\behaviors;

use Yii;
use yii\base\Behavior;
use parker714\yii2s\components\Response;

/**
 * Class ResponseLog
 *
 * @package parker714\yii2s\behaviors
 */
class ResponseLog extends Behavior
{
    /**
     * Response components beforeSend event
     *
     * @return array
     */
    public function events()
    {
        return [
            Response::EVENT_BEFORE_SEND => 'beforeSend',
        ];
    }

    /**
     * Response log action
     */
    public function beforeSend()
    {
        $log = [
            'request'  => Yii::$app->request->getInfo(),
            'response' => $this->owner->data,
        ];
        Yii::info(json_encode($log));
    }
}