<?php

namespace common\components;

use yii\helpers\Inflector;
use yii\base\BootstrapInterface;

class InflectorBootstrap implements BootstrapInterface
{
    /**
     * @param \yii\base\Application $app
     */
    public function bootstrap($app)
    {
        Inflector::$transliterator = 'Russian-Latin/BGN; NFKD';
    }
}