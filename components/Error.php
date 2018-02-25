<?php
namespace app\components;
use Yii;
use yii\base\ErrorHandler;
class Error extends ErrorHandler
{
    /**
     * 调试模式
     * @var bool
     */
    public $debug = false;
    /**
     * 生产环境错误码
     * @var int
     */
    public $prodErrCode = 50000;
    /**
     * 生产环境错误消息
     * @var string
     */
    public $prodErrMsg = '系统繁忙,请稍后重试!';
    /**
     * 错误处理
     * @param \Exception $exception
     */
    public function renderException($exception) {
        //Yii::error(self::convertExceptionToString($exception));
//        if (PHP_SAPI === 'cli') {
//            return;
//        }

        var_dump("111111");
        return $this->webResponse($exception);
    }
    /**
     * web应用异常响应
     * @param \Exception $e
     */
    private function webResponse(\Exception $e) {
        $resp       = Yii::$app->getResponse();
        $resp->data = [
            'err_code' => $this->prodErrCode,
            'err_msg'  => $this->prodErrMsg,
        ];
        if ($this->debug) {
            $resp->data = [
                'err_code' => $e->getCode(),
                'err_msg'  => $e->getMessage(),
                'err_file' => $e->getFile(),
                'err_line' => $e->getLine()
            ];
        }
        return $resp;
    }
}