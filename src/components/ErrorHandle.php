<?php

namespace degree757\yii2s\components;

use Yii;
use yii\base\ErrorException;
use yii\base\ErrorHandler;
use yii\db\Exception as DbException;
use yii\helpers\VarDumper;

class ErrorHandle extends ErrorHandler {
    public function renderException($exception) {
        if ($exception instanceof DbException) {
            Yii::$app->db->close();
        }
        
        $data['code'] = $exception->getCode();
        $data['msg']  = $exception->getMessage();
        if (YII_DEBUG) {
            $data['debug'] = $this->getInfo();
        }
        
        Yii::$app->response->data = $data;
        Yii::$app->response->send();
    }
    
    public function getInfo(){
        return ['request_info' => Yii::$app->request->getInfo(),
                'error_code'   => $this->exception->getCode(),
                'error_file'   => $this->exception->getFile(),
                'error_line'   => $this->exception->getLine(),
                'error_msg'    => $this->exception->getMessage(),
                'error_trace'  => explode(PHP_EOL, $this->exception->getTraceAsString())];
    }
    
    public function handleException($exception) {
        $this->exception = $exception;
        try {
            $this->logException($exception);
            if ($this->discardExistingOutput) {
                $this->clearOutput();
            }
            $this->renderException($exception);
        } catch (\Exception $e) {
            $this->handleFallbackExceptionMessage($e, $exception);
        } catch (\Throwable $e) {
            $this->handleFallbackExceptionMessage($e, $exception);
        }
        
        $this->exception = null;
    }
    
    public function handleError($code, $message, $file, $line) {
        if (error_reporting() & $code) {
            if (!class_exists('yii\\base\\ErrorException', false)) {
                require_once Yii::getAlias('@yii/base/ErrorException.php');
            }
            $exception = new ErrorException($message, $code, $code, $file, $line);
            
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            array_shift($trace);
            
            foreach ($trace as $frame) {
                if ($frame['function'] === '__toString') {
                    $this->handleException($exception);
                }
            }
            throw $exception;
        }
        
        return false;
    }
    
    public function handleFatalError() {
        if (!class_exists('yii\\base\\ErrorException', false)) {
            require_once Yii::getAlias('@yii/base/ErrorException.php');
        }
        
        $error = error_get_last();
        if (ErrorException::isFatalError($error)) {
            $exception       = new ErrorException($error['message'], $error['type'], $error['type'], $error['file'], $error['line']);
            $this->exception = $exception;
            
            $this->logException($exception);
            
            if ($this->discardExistingOutput) {
                $this->clearOutput();
            }
            $this->renderException($exception);
            
            Yii::getLogger()
               ->flush(true);
        }
    }
    
    public function handleFallbackExceptionMessage($exception, $previousException) {
        $msg = "An Error occurred while handling another error:\n";
        $msg .= (string)$exception;
        $msg .= "\nPrevious exception:\n";
        $msg .= (string)$previousException;
        if (YII_DEBUG) {
            if (PHP_SAPI === 'cli') {
                echo $msg . "\n";
            }
            else {
                echo '<pre>' . htmlspecialchars($msg, ENT_QUOTES, Yii::$app->charset) . '</pre>';
            }
        }
        else {
            echo 'An internal server error occurred.';
        }
        $msg .= "\n\$_SERVER = " . VarDumper::export($_SERVER);
        error_log($msg);
    }
}