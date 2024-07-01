<?php

declare(strict_types=1);

namespace backend\MentalMap;

use yii\db\ActiveRecord;

/**
 * @property string $uuid [varchar(36)]
 * @property string $name [varchar(255)]
 * @property string $type [varchar(255)]
 * @property string $key [varchar(1024)]
 * @property int $created_at [int(11)]
 * @property string $mental_map_id [varchar(36]
 */
class MentalMapImage extends ActiveRecord
{
    public static function create(string $uuid, string $name, string $type, string $key, string $mentalMapId = null): self
    {
        $model = new self();
        $model->uuid = $uuid;
        $model->name = $name;
        $model->type = $type;
        $model->key = $key;
        $model->created_at = time();
        $model->mental_map_id = $mentalMapId;
        return $model;
    }
}
