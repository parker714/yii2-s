<?php

namespace app\components;

use Yii;
use app\exceptions\Base;
use yii\base\ExitException;

class ErrorHandler extends \yii\web\ErrorHandler
{
    public function renderException($exception){
        $data = [
            'err_code' => 10000,
            'err_msg' => 'system busy'
        ];
        
        if($exception instanceof Base){
            $data['err_code'] = $exception->getCode();
            $data['err_msg'] = $exception->getMessage();
        }
        
        Yii::$app->response->data = $data;
        Yii::$app->response->send();
    }
    
    // sw 程序中禁止使用exit/die
    public function handleException($exception)
    {
        if ($exception instanceof ExitException) {
            return;
        }
        
        $this->exception = $exception;
        
        // disable error capturing to avoid recursive errors while handling exceptions
        $this->unregister();
        
        // set preventive HTTP status code to 500 in case error handling somehow fails and headers are sent
        // HTTP exceptions will override this value in renderException()
        if (PHP_SAPI !== 'cli') {
            http_response_code(500);
        }
        
        try {
            $this->logException($exception);
            if ($this->discardExistingOutput) {
                $this->clearOutput();
            }
            $this->renderException($exception);
            if (!YII_ENV_TEST) {
                \Yii::getLogger()->flush(true);
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
}