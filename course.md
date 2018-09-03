# Yii2 应用 swoole 的两种思路

---

我们理解和使用yii2和swoole的过程中，总会有一些疑惑、想法。现在记录下来，整理笔记、知识，并将其中的价值传播给他人，分享知识。

本文将重点介绍: (*注: php 7.1 + swoole 1.9)

> * 1.yii2控制台程序如何应用swoole（tcp服务器）
> * 2.yii2restful程序如何应用swoole （http服务器）

---
### 1.yii2 控制台程序应用swoole

>在yii2控制台程序中，启动一个swoole tcp服务。（将yii2作为容器，其内运行sw服务）

1.1 在Yii2控制器方法中，写一个最简单tcp服务（参照swoole文档api）

```php 
<?php
namespace app\commands;
use yii\console\Controller;
class SwTcpController extends Controller
{
    // sw tcp 服务
    private $_tcp;
    
    // 入口函数
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

1.2 写一个tcp客户端测试脚本tcp_client.php（这里采用swoole官网tcp客户端栗子）

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

1.3 测试

启动Yii2控制台方法：./yii sw-tcp/run

运行tcp客户端测试脚本：php tcp_client.php

1.4 总结

至此一个基本应用就完成咯，然后我们就可以应用swoole和yii2功能组件的愉快的编写代码了。

---


### 2.yii2 restful应用swoole

>写一个swoole http 服务脚本，在其onRequest回调函数中，模拟fpm功能，运行yii2应用。（将sw作为容器，其内运行yii2服务）

2.1 这里我们只需要将index.php稍微改造一下即可，在sw中模拟yii2 restful应用请求环境。

``` php
<?php
class swHttp {
	// sw http server
	private $_http;
	// yii2 application
	private $_app;
    
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
   	
  	// sw http onRequest回调函数
	public function onRequest($request, $response) {
		$this->setAppRunEnv($request, $response);
		$this->_app->run();
    }
   // 模拟fpm功能
  	public function setAppRunEnv($request, $response) {
  		// Yii2 request组件保存$request
		Yii::$app->request->setSwRequest($request);
		// Yii2 response组件保存$response
		Yii::$app->response->setSwResponse($response);

		// 常驻服务需要清除信息
		Yii::$app->request->getHeaders()->removeAll();
		Yii::$app->response->clear();
       
       // 常驻服务需要清除信息
		foreach ($request->server as $k => $v) {
			$_SERVER[strtoupper($k)] = $v;
       }
       
       // 设置头信息
		foreach ($request->header as $name => $value) {
			Yii::$app->request->getHeaders()->set($name, $value);
       }
       // 设置请求参数
		Yii::$app->request->setQueryParams($request->get);
		Yii::$app->request->setBodyParams($request->post);
		$rawContent = $request->rawContent() ?: null;
		Yii::$app->request->setRawBody($rawContent);
       // 设置路由
		Yii::$app->request->setPathInfo($request->server['path_info']);
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
  'max_request'   => 1,
  'daemonize'     => 0,
];
(new swHttp())->run($swConf, $app);
```

2.2 可能的问题：
q1：模拟fpm功能是什么意思？

a1：每一次请求会在fpm中存储此次请求的相关信息（路由，get，post，server参数），而yii2 restful应用的运行正是依赖这些参数信息。

q2：为什么要在yii2 request、response组件中保存sw request、response对象？

a2：其一需要扩展组件的方法，其二yii2组件需要与sw兼容。<br>
举个栗子，yii2 的 respone组件，其底层最终通过echo 输出数据到客户端，而在swoole中，echo是输出到控制台，需要使用其$respone->end()方法才可以输出数据到客户端。所以需要改写yii2 的response组件底层的输出方法。

q3：如何重新yii2组件？

a3: 通过配置组件类即可，这里以响应组件为栗子，参考下列代码：

``` php
1.修改配置文件
'components' => [
        ...
        'response'   => [
        	// 这里配置为重写后的response组件类
            'class'  => \app\components\Response::class,
            'format' => \yii\web\Response::FORMAT_JSON,
        ],
        ...

2.具体的重写后的response组件代码：
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
                    // 这里需要使用sw response来发送头信息
                    $this->_swResponse->header($name, $value);
                }
            }
        }
		// 这里需要使用sw response来发送状态码
		$this->_swResponse->status($this->getStatusCode());
    }
    
    // 改写底层发送内容
    public function sendContent()
    {
        if ($this->stream === null) {
            if ($this->content) {
					//echo $this->content;
					// 这里需要使用sw response来输出
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
}
```

这里提一点，在yii2 request组件中我们是有两种思路可以实现参数获取：

​	1：将组件的getQueryParams方法进行重写。（栗子在上面）

​	2：不进行过多的底层重写，设置app运行环境变量。（即模拟fmp环境）

这里推荐第二种，环境信息统一管理，做最小的改动，便于维护。

2.3 总结

原来nginx -> fpm -> yii2 restful<br>
现在nginx -> sw  -> yii2 restful

整个思路关键在于sw onRequest事件中模拟yii2 restful应用需要的请求信息即可。

---

### 3 写在最后

tcp服务比较简单不在赘述，http服务相对来说更复杂写，但是明白其中解析流程，了解其中的生命周期，在结合一些简单的想法，也就不那么难了。

```
Yii2中的其他组件：
errorHandler组件（必须改写）:
sw禁止使用exit/die，所以需改写其中的异常退出方法。

Log组件:
​sw提供异步写文件，写一个异步写日志的组件，提高性能。

async组件:
​sw中的异步赋予yii2组件应用异步能力。