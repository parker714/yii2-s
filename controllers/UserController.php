<?php

namespace app\controllers;

use yii\base\Controller;

class UserController extends Controller
{
    public function actionGet()
    {
        // 参数获取

        // db操作

        // redis操作
        $user = [
            'name' => 'xi',
            'age' => 20
        ];

        1/0;

        return $user;
    }
}
