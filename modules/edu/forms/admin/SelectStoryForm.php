<?php

namespace modules\edu\forms\admin;

use yii\base\Model;

class SelectStoryForm extends Model
{

    public $story_id;

    public function rules(): array
    {
        return [
            ['story_id', 'required'],
            ['story_id', 'integer'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'story_id' => 'История',
        ];
    }
}
