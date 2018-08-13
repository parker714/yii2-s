process_name="swoole-yii2"
bin="/data/apps/swoole-yii2/bin/sw"

case $1 in
start)
    $bin
    ;;
stop)
    ps -eaf|grep ${process_name}"$" | grep -v "grep"|awk '{print $2}'|xargs kill -9
    ;;
reload)
    ps -eaf|grep ${process_name}"$" | grep -v "grep"|awk '{print $2}'|xargs kill -9
    $bin
    ;;
*)
    echo "Usage  : sh http.sh [start | stop | reload]"
    ;;
esac