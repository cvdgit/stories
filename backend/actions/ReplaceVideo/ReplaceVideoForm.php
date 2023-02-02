<?php

declare(strict_types=1);

namespace backend\actions\ReplaceVideo;

use yii\base\Model;

class ReplaceVideoForm extends Model
{
    public $replace_video_id;
    public $videos;
    public $story_id;

    public function rules(): array
    {
        return [
            ['replace_video_id', 'required'],
            ['story_id', 'integer'],
            ['replace_video_id', 'string'],
            ['videos', 'each', 'rule' => ['string', 'max' => 20]],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'replace_video_id' => 'Заменить на видео:',
        ];
    }
}
