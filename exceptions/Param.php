<?php
namespace app\exceptions;

class Param extends Base{
    const PARAM_ERR = 10000;
    
    public static $reasons = [
        self::PARAM_ERR => 'param err'
    ];
}