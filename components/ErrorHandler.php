<?php

namespace app\components;

use Yii;

class ErrorHandler extends \yii\base\ErrorHandler
{
    public function renderException($exception){}
    
    public function handleFatalError()
    {
        $error = error_get_last();
        
        Yii::getLogger()->flush(true);
        Yii::$app->response->data = [
            'err_code' => 10000,
            'err_msg' => 'system busy',
            'err' => $error,
        ];
        Yii::$app->response->send();
    }
}