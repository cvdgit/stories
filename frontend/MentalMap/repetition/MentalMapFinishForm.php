<?php

declare(strict_types=1);

namespace frontend\MentalMap\repetition;

use yii\base\Model;

class MentalMapFinishForm extends Model
{
    public $mental_map_id;

    public function rules(): array
    {
        return [
            [['mental_map_id'], 'string'],
        ];
    }
}
