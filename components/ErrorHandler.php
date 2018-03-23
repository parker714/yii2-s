<?php

namespace app\components;

use Yii;
use app\exceptions\Base;
use yii\base\ErrorException;
use yii\base\ExitException;

class ErrorHandler extends \yii\base\ErrorHandler {
    public function renderException($exception) {
        $data = ['err_code' => 10000,
                 'err_msg'  => 'system busy'];
        
        if ($exception instanceof Base) {
            $data['err_code'] = $exception->getCode();
            $data['err_msg']  = $exception->getMessage();
        }
        
        if (YII_DEBUG) {
            $data['debug'] = ['err_code'  => $exception->getCode(),
                              'err_mse'   => $exception->getMessage(),
                              'err_file'  => $exception->getFile(),
                              'err_line'  => $exception->getLine()];
        }
        
        Yii::$app->response->data = $data;
        Yii::$app->response->send();
    }
    
    // sw 程序中禁止使用exit/die
    public function handleException($exception) {
        if ($exception instanceof ExitException) {
            return;
        }
        
        $this->exception = $exception;
        
        // disable error capturing to avoid recursive errors while handling exceptions
        // $this->unregister();
        
        try {
            $this->logException($exception);
            if ($this->discardExistingOutput) {
                $this->clearOutput();
            }
            $this->renderException($exception);
            if (!YII_ENV_TEST) {
                \Yii::getLogger()
                    ->flush(true);
                if (defined('HHVM_VERSION')) {
                    flush();
                }
                // sw 程序中禁止使用exit/die
                //exit(1);
            }
        } catch (\Exception $e) {
            // an other exception could be thrown while displaying the exception
            $this->handleFallbackExceptionMessage($e, $exception);
        } catch (\Throwable $e) {
            // additional check for \Throwable introduced in PHP 7
            $this->handleFallbackExceptionMessage($e, $exception);
        }
        
        $this->exception = null;
    }
    
    // sw 程序中禁止使用exit/die
    public function handleError($code, $message, $file, $line)
    {
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
                    if (defined('HHVM_VERSION')) {
                        flush();
                    }
                    //exit(1);
                }
            }
            
            throw $exception;
        }
        
        return false;
    }
}