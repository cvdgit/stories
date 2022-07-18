<?php

namespace common\components\module;

use Yii;
use yii\base\BootstrapInterface;

final class Provider implements BootstrapInterface
{
    public function bootstrap($app): void
    {
        $container = Yii::$container;
        $container->setSingleton(Modules::class, static function() use ($app) {
            return new Modules($app);
        });
    }
}
