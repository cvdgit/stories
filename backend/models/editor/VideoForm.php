<?php


namespace backend\models\editor;


class VideoForm extends BaseForm
{

    public $video_id;
    public $seek_to;

    public function rules(): array
    {
        $rules = parent::rules();
        $rules = array_merge($rules, [
            ['video_id', 'string'],
            ['seek_to', 'integer'],
        ]);
        return $rules;
    }

    public function attributeLabels(): array
    {
        $labels = parent::attributeLabels();
        $labels = array_merge($labels, [
            'video_id' => 'Видео',
            'seek_to' => 'Начать с',
        ]);
        return $labels;
    }

}