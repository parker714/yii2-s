<?php
/**
 * Base exception.
 */

namespace app\exceptions;

class BaseException extends \Exception {
    const SYSTEM_ERR = 10000;
    
    public static $reasons = [
        self::SYSTEM_ERR => 'system busy'
    ];
    
    public function __construct($code = null, $message = null) {
        $this->code    = $code;
        $this->message = $message ? $message : self::getReason($code);
    }
    
    public static function getReason($code) {
        return isset(static::$reasons[$code]) ? static::$reasons[$code] : 'Unknown error code';
    }
}