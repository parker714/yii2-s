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
                    '*'           => [
                        [
                            'param1',
                            'required'
                        ]
                    ],
                    'user/create' => [
                        [
                            [
                                'param2',
                                'email'
                            ],
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
        //        while (1){
        //            var_dump("1");
        //        }
        return [
            'name' => 'a',
            'age'  => 22220
        ];
    }
    
    /**
     * POST /user
     */
    public function actionCreate() {
        // async
        //        Yii::$app->sw->task([
        //            'app\services\UserService',
        //            'sendEmail'
        //        ], 1001);
        
        return Yii::$app->request->getInfo();
    }
}