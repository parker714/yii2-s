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
        return [
            ['name' => 'a','age'=>20]
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