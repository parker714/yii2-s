<?php
/**
 * Created by PhpStorm.
 * User: PB
 * Date: 2018/2/25
 * Time: 17:00
 */
namespace app\Behaviors;

use yii\base\Behavior;

class Request extends Behavior
{
    public $_queryParams = [];

    public function setQueryParams($value){
        $this->_queryParams = array_merge($this->_queryParams, $value);
    }

    public function getQueryParams(){
        return $this->_queryParams;
    }

    public function get($name, $defaultValue = null) {
        return isset($this->_queryParams[$name]) ? $this->_queryParams[$name] : $defaultValue;
    }
}