<?php

namespace degree757\yii2s\components;

use Yii;
use yii\base\ErrorException;
use yii\base\ErrorHandler;
use yii\db\Exception as DbException;
use yii\helpers\VarDumper;

class ErrorHandle extends ErrorHandler {
    /**
     * @event ErrorEvent an event that is triggered at the beginning of [[renderException()]].
     */
    const EVENT_BEFORE_RENDER = 'beforeSend';
    /**
     * @event ErrorEvent an event that is triggered at the end of [[renderException()]].
     */
    const EVENT_AFTER_RENDER = 'afterSend';
    
    /**
     * Get error request env info
     *
     * @return array
     */
    public function getInfo() {
        return [
            'request_info' => Yii::$app->request->getInfo(),
            'error_code'   => $this->exception->getCode(),
            'error_file'   => $this->exception->getFile(),
            'error_line'   => $this->exception->getLine(),
            'error_msg'    => $this->exception->getMessage(),
            'error_trace'  => explode(PHP_EOL, $this->exception->getTraceAsString())
        ];
    }
    
    public function renderException($exception) {
        $this->exception = $exception;
        
        $this->trigger(self::EVENT_BEFORE_RENDER);
        if ($exception instanceof DbException) {
            Yii::$app->db->close();
        }
        $this->trigger(self::EVENT_AFTER_RENDER);
    }
    
    public function handleException($exception) {
        $this->exception = $exception;
        try {
            $this->renderException($exception);
        } catch (\Exception $e) {
            $this->handleFallbackExceptionMessage($e, $exception);
        } catch (\Throwable $e) {
            $this->handleFallbackExceptionMessage($e, $exception);
        }
        
        $this->exception = null;
    }
    
    public function handleError($code, $message, $file, $line) {
        if ($code) {
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
        throw new \Exception('Unknown error type');
    }
    
    public function handleFatalError() {
        if (!class_exists('yii\\base\\ErrorException', false)) {
            require_once Yii::getAlias('@yii/base/ErrorException.php');
        }
        
        $error = error_get_last();
        if (ErrorException::isFatalError($error)) {
            $exception = new ErrorException($error['message'], $error['type'], $error['type'], $error['file'], $error['line']);
        }
        else {
            $exception = new ErrorException('Unknown error type');
        }
        $this->exception = $exception;
        $this->renderException($exception);
        
        Yii::getLogger()
           ->flush(true);
    }
    
    public function handleFallbackExceptionMessage($exception, $previousException) {
        $msg = "An Error occurred while handling another error:\n";
        $msg .= (string)$exception;
        $msg .= "\nPrevious exception:\n";
        $msg .= (string)$previousException;
        throw new \Exception($msg);
    }
}