<?php

namespace app;

use Yii;
use yii\base\ExitException;

class Application extends \yii\web\Application {
    
    public function run() {
        try {
            $this->state = self::STATE_BEFORE_REQUEST;
            $this->trigger(self::EVENT_BEFORE_REQUEST);
            
            $this->state = self::STATE_HANDLING_REQUEST;
            $response    = $this->handleRequest($this->getRequest());
            
            $this->state = self::STATE_AFTER_REQUEST;
            $this->trigger(self::EVENT_AFTER_REQUEST);
            
            $this->state = self::STATE_SENDING_RESPONSE;
            $response->send();
            
            $this->state = self::STATE_END;
            
            return $response->exitStatus;
        } catch (ExitException $e) {
            $this->end($e->statusCode, isset($response) ? $response : null);
            return $e->statusCode;
        } catch (\Exception $exception) {
            Yii::$app->errorHandler->handleException($exception);
            return 0;
        } catch (\Throwable $errorException) {
            Yii::$app->errorHandler->handleException($errorException);
            return 0;
        }
    }
}