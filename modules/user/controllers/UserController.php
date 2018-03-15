<?php

namespace app\modules\user\controllers;

use yii\base\Controller;

class UserController extends Controller
{
    public function actionGet()
    {
        // ...
        $user = [
            'name'    => 'pb',
            'age'     => 18
        ];
        return $user;
    }
}
