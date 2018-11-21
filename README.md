# yii2-s
speed up yii2 restful by swoole

## install (php 7.1.16、swoole 1.9.23)
```
composer -vvv require degree757/yii2-s
```

## run http server
```
cp -R vendor/degree757/yii2-s/demo ./
./demo/bin/http start
```

## docker-compose
```
version: "3"

services:
  yii2-s:
    image: "degree757/swoole:1.10"
    ports:
      - "18757:18757"
    volumes:
      - "/Users/pb/Work/yii2-s:/yii2-s"
    command:
      ["php","/yii2-s/demo/bin/http","start"]
```

## course
[course.md](https://www.jianshu.com/p/9c2788ccf3c0)
