<?php


namespace common\bootstrap;


use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\rbac\ManagerInterface;

class Bootstrap implements BootstrapInterface
{

    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        $container = Yii::$container;
        $container->setSingleton(ManagerInterface::class, function () use ($app) {
            return $app->authManager;
        });
    }
}