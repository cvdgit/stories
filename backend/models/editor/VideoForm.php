<?php


namespace backend\models\editor;


class VideoForm extends BaseForm
{

    public $video_id;
    public $seek_to;
    public $duration = 0;
    public $mute;
    public $speed = 1;
    public $volume = 0.8;

    public function rules(): array
    {
        $rules = parent::rules();
        $rules = array_merge($rules, [
            ['video_id', 'string'],
            ['video_id', 'string'],
            [['seek_to', 'duration', 'speed', 'volume'], 'double'],
            [['mute'], 'integer'],
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
            'mute' => 'Отключить звук',
            'speed' => 'Скорость воспроизведения',
            'volume' => 'Громкость',
        ]);
        return $labels;
    }

    public static function videoSpeedArray(): array
    {
        return [
            '0.5' => 0.5,
            '0.75' => 0.75,
            '1' => 1,
            '1.25' => 1.25,
            '1.5' => 1.5,
            '1.75' => 1.75,
            '2' => 2,
        ];
    }

}