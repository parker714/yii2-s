<?php

namespace parker714\yii2s\behaviors;

use Yii;
use yii\base\Behavior;
use yii\base\Controller;
use yii\base\DynamicModel;
use yii\helpers\StringHelper;

/**
 * Class ParamsValidate
 * @package parker714\yii2s\behaviors
 */
class ParamsValidate extends Behavior
{
    /**
     * validate data
     * ```
     * 'requestHeadersFilter' => [
     *      'class' => ParamsValidate::class,
     *      'data' => \Yii::$app->request->getHeaders()->toArray(),
     *      ...
     * ]
     *
     * ```
     * @var
     */
    public $data;
    /**
     * validate rule, key use preg_match, rules (https://www.yiichina.com/doc/guide/2.0/tutorial-core-validators)
     * ```
     * 'requestHeadersFilter' => [
     *      'class' => ParamsValidate::class,
     *      'data' => \Yii::$app->request->getHeaders()->toArray(),
     *      'rules' => [
     *          '*' => [
     *              ['param1', 'required']
     *          ],
     *          'user/create' => [
     *              [['param2', 'param3'], 'required']
     *          ]
     *      ]
     * ]
     *
     * ```
     * @var array
     */
    public $rules = [];

    /**
     * err Func
     *
     * ```
     * 'requestHeadersFilter' => [
     *      'class'   => ParamsValidate::class,
     *      'data'    => \Yii::$app->request->getHeaders()->toArray(),
     *      'errFunc' => function($data){
     *          Yii::$app->response->setStatusCode(403);
     *          throw new RequestException(RequestException::INVALID_PARAM, $data);
     *      }
     * ]
     * ```
     *
     * @var
     */
    public $errFunc;

    private $_validateKey = [];

    public function events()
    {
        return [
            Controller::EVENT_BEFORE_ACTION => 'eventBeforeAction',
        ];
    }

    /**
     * eventBeforeAction
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function eventBeforeAction()
    {
        $url   = rtrim(Yii::$app->controller->action->getUniqueId(), '/');
        $rules = $this->getValidateRules($url);

        $this->setValidateKey($rules);
        $this->setValidateVal($this->data);

        $DynamicModel = DynamicModel::validateData($this->_validateKey, $rules);
        if ($DynamicModel->hasErrors()) {
            call_user_func($this->errFunc, $DynamicModel->getFirstErrors());
        }
    }

    public function getValidateRules($url)
    {
        $rule = [];

        foreach ($this->rules as $key => $value) {
            if (StringHelper::matchWildcard($key, $url)) {
                $rule = array_merge($rule, $value);
            }
        }

        return $rule;
    }

    public function setValidateKey($rules)
    {
        foreach ($rules as $rule) {
            if (is_array($rule[0])) {
                foreach ($rule[0] as $v) {
                    $this->_validateKey[$v] = '';
                }
                continue;
            }

            $this->_validateKey[$rule[0]] = '';
        }
    }

    public function setValidateVal($post)
    {
        foreach ($this->_validateKey as $k => $v) {
            if (isset($post[$k])) {
                $this->_validateKey[$k] = $post[$k];
            }
        }
    }
}