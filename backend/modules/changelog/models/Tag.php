<?php

declare(strict_types=1);

namespace backend\modules\changelog\models;

use yii\db\ActiveRecord;

/**
 * @property int $id [int(11)]
 * @property int $frequency [int(11)]
 * @property string $name [varchar(255)]
 */
class Tag extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'tag';
    }
}
