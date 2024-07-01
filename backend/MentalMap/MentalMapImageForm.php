<?php

declare(strict_types=1);

namespace backend\MentalMap;

use yii\base\Model;

class MentalMapImageForm extends Model
{
    public $mental_map_id;
    public $image;
    public $type;
    public $image_item_id;

    public function rules(): array
    {
        return [
            [['mental_map_id', 'image', 'type'], 'required'],
            [['mental_map_id', 'type', 'image_item_id'], 'string'],
            ['type', 'in', 'range' => ['mental_map', 'image']],
            ['image', 'image'],
        ];
    }
}
