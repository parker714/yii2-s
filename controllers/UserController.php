<?php

namespace app\controllers;

use Yii;
use app\services\TaskService;
use yii\rest\Controller;

class UserController extends Controller {
    public function behaviors() {
        return [];
    }
    
    public function init() {}
    
    // GET /users/11
    public function actionView() {
        // 模拟异步
        Yii::$app->sw->task(TaskService::class.'::sendEmail', rand(1,1000));
        
        return [
            'headers'   => Yii::$app->request->getHeaders(),
            'get'       => Yii::$app->request->getQueryParams(),
            'post'      => Yii::$app->request->getBodyParams(),
            ];
    }
}