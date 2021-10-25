<?php

namespace backend\components;

use DomainException;
use yii\db\ActiveRecord;

class BaseModel
{

    public static function saveModel(ActiveRecord $model, $runValidation = true): void
    {
        if (!$model->save($runValidation)) {
            $modelName = array_reverse(explode('\\', get_class($model)))[0];
            throw new DomainException("Не удалось сохранить модель $modelName. Ошибки: " . implode(', ', $model->getFirstErrors()));
        }
    }
}
