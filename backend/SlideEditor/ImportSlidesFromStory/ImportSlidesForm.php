<?php

declare(strict_types=1);

namespace backend\SlideEditor\ImportSlidesFromStory;

use yii\base\Model;

class ImportSlidesForm extends Model
{
    public $fromStoryId;
    public $toStoryId;
    public $currentSlideId;

    public function rules(): array
    {
        return [
            [['fromStoryId', 'toStoryId'], 'required'],
            [['fromStoryId', 'toStoryId', 'currentSlideId'], 'integer'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'fromStoryId' => 'Импортировать слайды из истории',
        ];
    }
}
