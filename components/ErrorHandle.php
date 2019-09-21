<?php

namespace parker714\yii2s\components;

use Yii;
use yii\base\ErrorException;
use yii\base\ErrorHandler;

/**
 * Class ErrorHandle
 *
 * @package parker714\yii2s\components
 */
class ErrorHandle extends ErrorHandler
{
    /**
     * @event ErrorEvent an event that is triggered at the end of [[renderException()]].
     */
    const EVENT_AFTER_RENDER = 'afterRender';

    /**
     * rewrite renderException
     *
     * @param \Exception $exception
     */
    public function renderException($exception)
    {
        $this->exception = $exception;
        $this->trigger(self::EVENT_AFTER_RENDER);
    }

    /**
     * rewrite handleException
     *
     * @param \Exception $exception
     *
     * @throws \Exception
     */
    public function handleException($exception)
    {
        $this->exception = $exception;
        try {
            $this->renderException($exception);
        } catch (\Exception $e) {
            $this->handleFallbackExceptionMessage($e, $exception);
        } catch (\Throwable $e) {
            $this->handleFallbackExceptionMessage($e, $exception);
        }
    }

    /**
     * rewrite handleError
     *
     * @param int    $code
     * @param string $message
     * @param string $file
     * @param int    $line
     *
     * @return bool|void
     * @throws ErrorException
     */
    public function handleError($code, $message, $file, $line)
    {
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

    /**
     * rewrite handleFatalError
     */
    public function handleFatalError()
    {
        if (!class_exists('yii\\base\\ErrorException', false)) {
            require_once Yii::getAlias('@yii/base/ErrorException.php');
        }

        $error = error_get_last();
        if (ErrorException::isFatalError($error)) {
            $exception = new ErrorException($error['message'], $error['type'], $error['type'], $error['file'], $error['line']);
        } else {
            $exception = new ErrorException('Unknown error type');
        }
        $this->exception = $exception;
        $this->renderException($exception);

        Yii::getLogger()
            ->flush(true);
    }

    /**
     * rewrite handleFallbackExceptionMessage
     *
     * @param \Exception|\Throwable $exception
     * @param \Exception            $previousException
     *
     * @throws \Exception
     */
    public function handleFallbackExceptionMessage($exception, $previousException)
    {
        $msg = "An Error occurred while handling another error:\n";
        $msg .= (string)$exception;
        $msg .= "\nPrevious exception:\n";
        $msg .= (string)$previousException;
        throw new \Exception($msg);
    }
}