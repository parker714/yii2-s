# Yii2 应用 swoole 的两种思路

------

我们理解和使用yii2和swoole的过程中，总会有一些疑惑、想法。现在记录下来，整理笔记、知识，并将其中的价值传播给他人，分享知识。

本文将重点介绍: (*注: 系统环境：php 7.1 + swoole 1.9)

> * 1.yii2控制台程序如何应用swoole（tcp服务器）
> * 2.yii2restful程序如何应用swoole （http服务器）


### 1.yii2 控制台程序应用swoole

核心思想：在yii2控制台程序中，启动一个swoole tcp服务。（将yii2作为容器，其内运行sw服务）

具体步骤：

1.1 在控制器方法中，参照swoole文档回调函数配置，写一个最简单tcp服务代码。

```php 
<?php
namespace app\commands;
use yii\console\Controller;
class SwTcpController extends Controller
{
    // sw tcp 服务
    private $_tcp;
    // 控制台应用方法
    public function actionRun()
    {
        $this->_tcp = new \swoole_server('0.0.0.0', 9503);
        $this->_tcp->on('connect', [$this, 'onConnect']);
        $this->_tcp->on('receive', [$this, 'onReceive']);
        $this->_tcp->on('close', [$this, 'onClose']);
        $this->_tcp->start();
    }
    // sw connect 回调函数
    public function onConnect($server, $fd)
    {
        echo "connection open: {$fd}\n";
    }
    // sw receive 回调函数
    public function onReceive($server, $fd, $reactor_id, $data)
    {
        // 向客户端发送数据
        $server->send($fd, "Swoole: {$data}");
        // 关闭客户端
        $server->close($fd);
    }
    // sw close 回调函数
    public function onClose($server, $fd)
    {
        echo "connection close: {$fd}\n";
    }
}
```

1.2 写一个tcp客户端测试脚本 tcp_client.php，这里采用swoole官网tcp客户端栗子。

``` php
<?php
// tcp client
$client = new \swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);
$client->on("connect", function ($cli) {
    $cli->send("hello world\n");
});
$client->on("receive", function ($cli, $data) {
    echo "received: {$data}\n";
});
$client->on("error", function ($cli) {
    echo "connect failed\n";
});
$client->on("close", function ($cli) {
    echo "connection close\n";
});
$client->connect("127.0.0.1", 9503, 0.5);
```

1.3：测试

启动tcp服务端：./yii sw-tcp/run

启动tcp客户端：php tcp_client.php

至此一个基本应用就完成了，然后我们就可以应用yii2和swoole功能组件的编写代码了。





### 2.yii2 restful应用swoole

核心思想：写一个swoole http 服务脚本，在其onRequest回调函数中，模拟fpm环境，运行yii2应用。（将sw作为容器，其内运行yii2服务）

具体步骤：

这里我们只需要将index.php稍微改造一下即可，在sw中模拟yii2 restful应用请求环境。

``` php
<?php
class swHttp {
  	private $_app;
    private $_http;
  	// 程序启动入口
    public function run($conf, $app) {
      	$this->_app = $app;
      
        $this->_http = new \swoole_http_server('0.0.0.0', 9501);
        $this->_http->on('start', [$this,'onStart']);
        $this->_http->on('WorkerStart', [$this,'onWorkerStart']);
        $this->_http->on('request', [$this,'onRequest']);
        $this->_http->set($conf);  
        $this->_http->start();
    }

    public function onStart($server) {}
    public function onWorkerStart($server, $worker_id) {}
   	
  	// sw http请求事件
    public function onRequest($request, $response) {
        $this->setAppRunEnv($request, $response);
        $this->_app->run();
    }
  	public function setAppRunEnv($request, $response) {
      	// 清除上一次响应信息（常驻服务，避免影响）
        $this->_app->response->clear();
      	// 在yii2 request组件中保存sw request（用于兼容）
        $this->_app->request->setSwRequest($request);
      	// 在yii2 response组件中保存sw respone（用于兼容）
        $this->_app->response->setSwResponse($response);
      	// 设置yii2应用请求所需要的环境
        $this->_app->request->setRequestEnv();
    }
}
//----------------- yii2 web application--------
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');
require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../web/Application.php');
$config = include(__DIR__ . '/../config/web.php');
$app = new \app\web\Application($config);
//----------------- sw config -------------------
$swConf = [
  'pid_file'      => __DIR__ . '/server.pid',
  'worker_num'    => 4,
  'max_request'   => 1000,
  'daemonize'     => 0,
];
(new swHttp())->run($swConf, $app);
```

答疑解惑：

问：为什么要调yii2 response组件的clear方法？

答：常驻服务需要清除信息，避免影响。

问：为什么要在yii2 request、response组件中保存sw request。response对象？

答：其一需要扩展组件的方法，其二yii2组件需要与sw兼容。

​	举个栗子，yii2 的 respone组件，其底层最终通过echo 输出数据到客户端，而在swoole中，echo 是输出到控制台，	需要使用其$respone->end()方法才可以输出数据到客户端。所以需要改写yii2 的response组件底层的输出方法。

问：yii2 restful应用需要依赖那些环境变量？

答：包括url，get、post参数，header参数信息等等

问：那组件兼容如何写？

答：通过配置组件类即可，这里以响应组件为栗子，参考下列代码：

``` php
'components' => [
        ...
        'response'   => [
        	// 这里配置为重写后的response组件类
            'class'  => \app\components\Response::class,
            'format' => \yii\web\Response::FORMAT_JSON,
        ],
```

具体的重写后的response组件代码：

```php
<?php
namespace app\components;
// 继承底层 web response
class Response extends \yii\web\Response
{
  	// 存储sw response对象
    private $_swResponse;
    public function setSwResponse($response)
    {
        $this->_swResponse = $response;
    }
    public function getSwResponse()
    {
        return $this->_swResponse;
    }
    // 改写底层发送头信息
    public function sendHeaders()
    {
        $headers = $this->getHeaders();
        if ($headers->count > 0) {
            foreach ($headers as $name => $values) {
                $name = str_replace(' ', '-', ucwords(str_replace('-', ' ', $name)));
                foreach ($values as $value) {   
                    // 这里使用sw response来发送头信息
                    $this->_swResponse->header($name, $value);
                }
            }
        }
      	// 这里使用sw response来发送状态码
        $this->_swResponse->status($this->getStatusCode());
    }
    // 改写底层发送内容
    public function sendContent()
    {
        if ($this->stream === null) {
            if ($this->content) {
              	// *** 输出数据到客户端，这里需要使用sw response对象来输出
                //echo $this->content;
                $this->_swResponse->end($this->content);
            } else {
                $this->_swResponse->end();
            }
            return;
        }
        $chunkSize = 2 * 1024 * 1024; // 2MB per chunk swoole limit
        if (is_array($this->stream)) {
            list ($handle, $begin, $end) = $this->stream;
            fseek($handle, $begin);
            while (!feof($handle) && ($pos = ftell($handle)) <= $end) {
                if ($pos + $chunkSize > $end) {
                    $chunkSize = $end - $pos + 1;
                }
                // 使用sw response对象来输出
                $this->_swResponse->write(fread($handle, $chunkSize));
                flush(); // Free up memory. Otherwise large files will trigger PHP's memory limit.
            }
            fclose($handle);
        } else {
            while (!feof($this->stream)) {
                $this->_swResponse->write(fread($this->stream, $chunkSize));
                flush();
            }
            fclose($this->stream);
        }
        // 使用sw response对象来输出
        $this->_swResponse->end();
    }
}
```

然后还有我们常用的request组件:

```php
<?php
namespace app\components;
class Request extends \yii\web\Request {
   // 存储 sw request对象
    private $_swRequest;
    
    public function setSwRequest($request) {
        $this->_swRequest = $request;
    }
    public function getSwRequest() {
        return $this->_swRequest;
    }
    /***
    // 重写底层方法的栗子
    public function getQueryParams()
    {
        if ($this->_queryParams === null) {
            //return $_GET;
            // 重写其参数获取方式
          	return $this->_swRequest->get;
        }
        return $this->_queryParams;
    }
    ***/
  
    // 模拟fpm请求环境，通过yii2内置方法设置环境信息（如果还需要其他信息，自行添加即可）
    public function setRequestEnv() {
        // 设置头信息
        $this->getHeaders()->removeAll();
        foreach ($this->_swRequest->header as $name => $value) {
            $this->getHeaders()->add($name, $value);
        }
        // 设置参数信息
        $_GET                      = isset($this->_swRequest->get) ? $this->_swRequest->get : [];
        $_POST                     = isset($this->_swRequest->post) ? $this->_swRequest->post : [];
        $_SERVER['REQUEST_METHOD'] = $this->_swRequest->server['request_method'];
        $this->setBodyParams(null);
        $this->setRawBody($this->_swRequest->rawContent());
        // 设置路由
        $this->setPathInfo($this->_swRequest->server['path_info']);
    }
}
```

这里提一点，在yii2 request组件中我们是有两种方式可以实现参数获取的：

​	1：将组件的getQueryParams方法进行重写。（一般做法，看上面栗子）

​	2：不进行重写，设置全局环境变量（aop思想，如将sw接收到的get数据放置 $_GET中，看上面栗子）

这里推荐第二种，环境信息统一管理，需要改动较小，便于维护。



还有一些Yii2中的其他组件：

**errorHandler组件：**

 sw 程序中禁止使用exit/die 方法，所以 ErrorHandler 需要改写其中的异常退出方法。

**Log 组件：** 

​	sw提供异步写文件，这里是不是可以改进一下，写一个自己的异步写日志的组件，提高性能。

**sw中的异步：**

​	赋予yii2应用异步能力。

### 3.结尾

tcp服务不在赘述，http服务相对来说更复杂写，但是明白其中思想（onRequest事件 -> 设置yii2应用环境-> 运行yii2应用）和解析过程，以及相关组件兼容写法，也就不那么难了。
