<?php

namespace app\exceptions;

class Base extends \Exception {
    public static $reasons = [];
    public $data;
    
    public function __construct($code, $data = null) {
        $this->code    = $code;
        $this->message = self::getReason($code);
        
        if ($data) {
            $this->data = $data;
        }
    }
    
    public static function getReason($code) {
        return isset(static::$reasons[$code]) ? static::$reasons[$code] : 'Unknown error code';
    }
}