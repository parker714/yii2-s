<?php
/**
 * Request exception.
 */

namespace app\exceptions;

class RequestException extends BaseException {
    const URI_ERR            = 10001;
    const PERMISSION_DENIED  = 10002;
    const INVALID_SIGNATURE  = 10003;
    const INVALID_PARAM      = 10004;
    const REPEAT_REQUEST     = 10005;
    const REQUEST_TIMEOUT    = 10006;
    const UNAUTHORIZED_TOKEN = 10007;
    
    public static $reasons = [
        self::URI_ERR            => 'uri not exist',
        self::PERMISSION_DENIED  => 'permission denied',
        self::INVALID_SIGNATURE  => 'invalid signature',
        self::INVALID_PARAM      => 'invalid param',
        self::REPEAT_REQUEST     => 'repeated request',
        self::REQUEST_TIMEOUT    => 'request timeout',
        self::UNAUTHORIZED_TOKEN => 'unauthorized token'
    ];
}