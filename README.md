# swoole-yii2
两种思路将swoole 与 yii2 结合 

1. 通过yii2控制台应用程序加载swoole (tcp server)

2. 通过swoole脚本模拟运行yii2Web应用 (http server)

详细教程：https://github.com/degree24/swoole-yii2/blob/master/course.md

# install
composer create-project degree66/swoole-yii2

# run
1.控制台应用运行（tcp-server）

./yii sw-tcp/run

2.脚本运行（http-server）

php bin/sw.php

# benchmark
4C 8G 8worker
ab -c 5000 -n 1000000

PHP 5.6
10552.19qps
PHP 7.2.3
12957.31qps

# 备注
微信扫一扫：<br>
<img src="https://raw.githubusercontent.com/degree66/swoole-yii2/master/web/pay.png" width = "150" height = "150" />
