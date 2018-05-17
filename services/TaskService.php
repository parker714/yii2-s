<?php
/**
 * 异步任务
 */
namespace app\services;

class TaskService {
    public static function sendEmail($userId){
        //sleep(3);
        var_dump("task: send {$userId} email");
    }
}