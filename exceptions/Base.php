<?php
namespace app\exceptions;

class Base extends \Exception
{
    protected $code = 10000;
    
    protected $message = 'system busy';
}