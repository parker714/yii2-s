<?php

namespace parker714\yii2s;

use yii\base\BootstrapInterface;

/**
 * Class Bootstrap
 * @package parker714\yii2s
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * @param \yii\base\Application $app
     */
    public function bootstrap($app)
    {
        if ($app instanceof \yii\console\Application) {
        }
    }
}
