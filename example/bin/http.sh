process_name="yii2-s"
bin="/data/apps/yii2-s/bin/sw"

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
    echo "Usage: sh http.sh [start | stop | reload]"
    ;;
esac