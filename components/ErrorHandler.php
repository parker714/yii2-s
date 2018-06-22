<?php

namespace app\components;

use Yii;
use app\exceptions\Base;
use yii\base\ErrorException;

class ErrorHandler extends \yii\base\ErrorHandler {
    public $prodCode = 10000;
    public $prodMsg  = 'system busy';
    
    public function renderException($exception) {
        $data = ['code' => $this->prodCode,
                 'msg'  => $this->prodMsg];
        
        if ($exception instanceof Base) {
            $data['code'] = $exception->getCode();
            $data['msg']  = $exception->getMessage();
        }
        
        if (YII_DEBUG) {
            $data['debug'] = ['requestRoute' => Yii::$app->requestedRoute,
                              'file' => $exception->getFile(),
                              'line' => $exception->getLine(),
                              'msg'  => $exception->getMessage()];
        }
        
        Yii::$app->response->data = $data;
        Yii::$app->response->send();
    }
    
    public function handleException($exception) {
        $this->renderException($exception);
    }
    
    public function handleError($code, $message, $file, $line) {
        if (!class_exists('yii\\base\\ErrorException', false)) {
            require_once Yii::getAlias('@yii/base/ErrorException.php');
        }
        $exception = new ErrorException($message, $code, $code, $file, $line);
        
        // in case error appeared in __toString method we can't throw any exception
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        array_shift($trace);
        foreach ($trace as $frame) {
            if ($frame['function'] === '__toString') {
                $this->handleException($exception);
            }
        }
        
        throw $exception;
    }
    
    public function handleFatalError() {
        if (!class_exists('yii\\base\\ErrorException', false)) {
            require_once Yii::getAlias('@yii/base/ErrorException.php');
        }
        
        $error     = error_get_last();
        $exception = new ErrorException($error['message'], $error['type'], $error['type'], $error['file'], $error['line']);
        $this->renderException($exception);
    }
}