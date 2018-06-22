<?php
/**
 * 异步任务
 */
namespace app\services;

class TaskService {
    public static function sendEmail($userId){
        // 模拟耗时
        sleep(3);
        echo "task: send email #{$userId} ";
    }
}