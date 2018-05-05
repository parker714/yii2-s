<?php

namespace app\components;

use Yii;
use app\exceptions\Base;
use yii\base\ErrorException;
use yii\base\ExitException;

class ErrorHandler extends \yii\base\ErrorHandler {
    public function renderException($exception) {
        $data = ['code' => 10000,
                 'msg'  => 'system busy'];
        
        if ($exception instanceof Base) {
            $data['code'] = $exception->getCode();
            $data['msg']  = $exception->getMessage();
        }
        
        if (YII_DEBUG) {
            $data['debug'] = ['code' => $exception->getCode(),
                              'msg'  => $exception->getMessage(),
                              'file' => $exception->getFile(),
                              'line' => $exception->getLine()];
        }
        
        Yii::$app->response->data = $data;
        Yii::$app->response->send();
    }
    
    public function handleException($exception) {
        if ($exception instanceof ExitException) {
            return;
        }
        
        $this->exception = $exception;
        $this->renderException($exception);
        $this->exception = null;
    }
    
    public function handleError($code, $message, $file, $line) {
        if (error_reporting() & $code) {
            // load ErrorException manually here because autoloading them will not work
            // when error occurs while autoloading a class
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
                    //exit(1);
                }
            }
            
            throw $exception;
        }
        
        return false;
    }
    
    public function handleFatalError() {
        // load ErrorException manually here because autoloading them will not work
        // when error occurs while autoloading a class
        if (!class_exists('yii\\base\\ErrorException', false)) {
            require_once Yii::getAlias('@yii/base/ErrorException.php');
        }
        
        $error = error_get_last();
        if (ErrorException::isFatalError($error)) {
            $exception       = new ErrorException($error['message'], $error['type'], $error['type'], $error['file'], $error['line']);
            $this->exception = $exception;
            $this->renderException($exception);
        }
    }
}