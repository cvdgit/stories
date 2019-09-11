<?php


namespace backend\models\editor;


class VideoForm extends BaseForm
{

    public $video_id;
    public $seek_to;
    public $duration;

    public function rules(): array
    {
        $rules = parent::rules();
        $rules = array_merge($rules, [
            ['video_id', 'string'],
            [['seek_to', 'duration'], 'integer'],
        ]);
        return $rules;
    }

    public function attributeLabels(): array
    {
        $labels = parent::attributeLabels();
        $labels = array_merge($labels, [
            'video_id' => 'Видео',
            'seek_to' => 'Начать с (сек)',
            'duration' => 'Продолжительность (сек)',
        ]);
        return $labels;
    }

}