<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use app\services\TaskService;
use app\exceptions\Param;

class UserController extends Controller {
    public function behaviors() {
        return [];
    }
    
    /**
     * GET /users
     *
     * @return array
     */
    public function actionIndex() {
        // test task
        //Yii::$app->sw->task(TaskService::class.'::sendEmail', rand(1,1000));
        
        // test exception
        //throw new Param(Param::PARAM_ERR);
        //1/0;
        
        // test error
        //error_reporting(0);
        //trigger_error('trigger error',E_USER_ERROR);
        
        return ['get' => Yii::$app->request->get()];
    }
    
    /**
     * POST /users
     *
     * @return array
     */
    public function actionCreate() {
        return ['get'  => Yii::$app->request->get(),
                'post' => Yii::$app->request->post()];
    }
}