<?php

namespace app\services;

class EmailService {
    /**
     * @param $userId
     * @param $content
     */
    public static function sendEmail($userId, $content) {
        sleep(5);
        echo "send email to {$userId}, {$content}\n";
    }
}