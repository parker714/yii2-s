<?php
namespace app\components;

use yii\log\FileTarget;

class Log extends FileTarget {
    public function export()
    {
        //$text = implode("\n", $this->messages) . "\n";

        file_put_contents('a.txt','xxx'.PHP_EOL, FILE_APPEND);

//        swoole_async_writefile('test.log', $text, function($filename) {
//        }, $flags = 0);
    }
}