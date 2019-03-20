<?php

namespace degree757\yii2s;

use Yii;
use yii\console\Controller;
use degree757\yii2s\servers\Server;
use yii\helpers\Console;

/**
 * Class SwController
 * @package degree757\yii2s
 */
class SwController extends Controller
{
    /**
     * Sw server instance
     * @var Server
     */
    public $server;

    /**
     * Init sw server instance
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();

        $this->server                  = Yii::createObject($this->server);
        $this->server->set['pid_file'] = Yii::getAlias($this->server->set['pid_file']);
        $this->server->set['log_file'] = Yii::getAlias($this->server->set['log_file']);
    }

    /**
     * Sw server dispatcher
     * @param $command
     * @return bool|int
     */
    public function actionServer($command)
    {
        $pidFile = $this->server->getPidFile();
        $pid     = file_exists($pidFile) ? file_get_contents($pidFile) : null;

        switch ($command) {
            case 'start':
                if (!is_null($pid) && posix_kill($pid, 0)) {
                    return $this->stdout("server(pid: {$pid}) already start\n", Console::FG_RED);
                }
                $this->server->start();
                break;
            case 'stop':
                if (!is_null($pid)) {
                    posix_kill($pid, SIGTERM);
                }
                break;
            case 'reload':
                if (!is_null($pid)) {
                    // reload all worker
                    posix_kill($pid, SIGUSR1);
                    // reload all task worker
                    //posix_kill($pid, SIGUSR2);
                }
                break;
            default:
                $this->stdout("Unknown command, usage: start|stop|reload\n", Console::FG_RED);
        }
    }
}