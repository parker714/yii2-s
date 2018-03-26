# swoole-yii2
两种思路将swoole 与 yii2 结合 

1. 通过yii2控制台应用程序加载swoole (tcp server)

2. 通过swoole脚本模拟运行yii2应用 (http server)

# install
composer create-project degree66/swoole-yii2

# run
1.控制台运行（tcp-server）

./yii sw-tcp/run

2.脚本运行（http-server）

php bin/swHttp.php

# benchmark
4C 8G 8worker

ab -c 5000 -n 1000000

PHP 5.6
with log 7271.29qps 

without log 10552.19qps

PHP 7.2.3
with log 10327.91qps
 
without log 12957.31qps

# 备注
详细教程：http://www.yiichina.com/tutorial/1641

有问题加群: 218327228

微信扫一扫：
![degree san](https://raw.githubusercontent.com/degree66/swoole-yii2/master/web/pay.png)



