<?php

declare(strict_types=1);

namespace common\components;

use Ramsey\Uuid\Uuid;
use yii\validators\Validator;

class UuidValidator extends Validator
{
    public function validateAttribute($model, $attribute): void
    {
        $value = $model->$attribute;
        if (!Uuid::isValid($value)) {
            $this->addError($model, $attribute, 'Uuid is invalid');
        }
    }
}
