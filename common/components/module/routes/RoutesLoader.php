<?php

namespace common\components\module\routes;

use Yii;
use yii\base\BootstrapInterface;

final class RoutesLoader implements BootstrapInterface
{
    public function bootstrap($app): void
    {
        $container = Yii::$container;
        $fetcher = $container->get(RoutesFetcher::class);
        $urlManager = Yii::$app->urlManager;
        $urlManager->addRules($fetcher->getRules());
    }
}
