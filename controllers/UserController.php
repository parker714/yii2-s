<?php

namespace app\controllers;

use app\exceptions\Param;
use Yii;
use yii\rest\Controller;

class UserController extends Controller {
    public function behaviors() {
        return [];
    }
    
    // POST /users
    public function actionCreate() {
        // ...
        return ['get'       => Yii::$app->request->getQueryParams(),
                'post'      => Yii::$app->request->getBodyParams(),
                'raw'       => Yii::$app->request->getRawBody(),
                'headers'   => Yii::$app->request->getHeaders(),
                'headers-a' => Yii::$app->request->getHeaders()->get('a')];
    }
    
    // GET /users/11
    public function actionView() {
        //1/0;
        //throw new Base();
        //throw new Param();
        
        return ['get' => Yii::$app->request->getQueryParams(),];
    }
}