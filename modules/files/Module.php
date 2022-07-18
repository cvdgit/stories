<?php

namespace modules\files;

use common\components\module\routes\RoutesProvider;
use yii\base\Module as Base;

/**
 * files module definition class
 */
class Module extends Base implements RoutesProvider
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'modules\files\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }

    public static function routes(): array
    {
        return [
            'file/<id:[A-Za-z0-9_-]+>' => 'files/default/get',
        ];
    }

    public static function routesPriority(): int
    {
        return 0;
    }
}
