<?php
/**
 * Sw Server Dispatcher
 */

namespace degree757\yii2s\servers;

class Dispatcher {
    public $server;
    
    public function __construct($conf) {
        $this->server = new $conf['class']($conf);
    }
    
    public function run() {
        global $argv;
        
        $pidFile = $this->server->getPid();
        $pid     = file_exists($pidFile) ? file_get_contents($pidFile) : null;
        
        $command = isset($argv[1]) ? $argv[1] : null;
        switch ($command) {
            case 'start':
                if (!is_null($pid) && posix_kill($pid, 0)) {
                    exit('server already start.');
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
                print_r("yii2-s Usage:\n\tphp http.php start|stop|reload\n");
        }
    }
}