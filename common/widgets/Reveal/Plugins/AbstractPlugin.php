<?php

declare(strict_types=1);

namespace common\widgets\Reveal\Plugins;

use yii\base\BaseObject;

abstract class AbstractPlugin extends BaseObject
{
    public $configName;
    public $config = [];
}
