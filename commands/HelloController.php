<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;


class HelloController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     * @return int Exit code
     */
    public function actionIndex($message = 'hello world')
    {
        echo $message . PHP_EOL;

        return ExitCode::OK;
    }

    public function actionWorld(){
        $data =  [
            'name'=>'xx',
            'age'=>18
        ];

        var_dump(Yii::$app->request->getQueryParams());

        return $data;
    }
}
