<?php

namespace common\components;

use DomainException;
use yii\base\Model;

class ModelDomainException
{

    public static function create(Model $model): DomainException
    {
        return new DomainException(implode(PHP_EOL, $model->getErrorSummary(true)));
    }
}
