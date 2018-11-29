<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use app\exceptions\RequestException;
use degree757\yii2s\behaviors\ParamsValidate;

class UserController extends Controller {
    public function behaviors() {
        return [
            'requestParamsValidate' => [
                'class'   => ParamsValidate::class,
                'data'    => array_merge(Yii::$app->request->get(), Yii::$app->request->post()),
                'rules'   => [
                    'user/create' => [
                        [
                            'email',
                            'required'
                        ],
                        [
                            'email',
                            'email'
                        ],
                    ],
                ],
                'errFunc' => function ($data) {
                    throw new RequestException(reset($data), RequestException::INVALID_PARAM);
                },
            ],
        ];
    }
    
    /**
     * GET /user
     */
    public function actionIndex() {
        return [
            Yii::$app->request->get(),
            Yii::$app->request->post()
        ];
    }
    
    /**
     * POST /user
     *
     * @return array
     * @CreateTime 2018/11/13 19:00:25
     */
    public function actionCreate() {
        // async
        Yii::$app->sw->task([
            'app\services\UserService',
            'sendEmail'
        ], 1, "hello");
        
        return [
            Yii::$app->request->get(),
            Yii::$app->request->post()
        ];
    }
}