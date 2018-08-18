<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;

class UserController extends Controller {
    public function behaviors() {
        return [];
    }
    
    /**
     * GET /user
     */
    public function actionIndex() {
        1/0;
        
//        while (1){
//            var_dump("1");
//        }
        return [
            'name' => 'a',
            'age' => 22220
        ];
    }
    
    /**
     * POST /user
     */
    public function actionCreate(){
        // 1/0;
        
        // async
        Yii::$app->sw->task(['app\services\UserService', 'sendEmail'], 1001);
        
        return Yii::$app->request->getInfo();
    }
}