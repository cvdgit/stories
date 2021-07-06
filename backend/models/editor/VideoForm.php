<?php

namespace backend\models\editor;

use backend\models\video\VideoSource;

class VideoForm extends BaseForm
{

    public $video_id;
    public $seek_to;
    public $duration = 0;
    public $mute;
    public $speed = 1;
    public $volume = 0.8;
    public $to_next_slide;

    public $source;

    public function rules()
    {
        return array_merge(parent::rules(), [
            ['video_id', 'string'],
            ['video_id', 'string'],
            [['seek_to', 'duration', 'speed', 'volume'], 'double'],
            [['mute', 'to_next_slide', 'source'], 'integer'],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'video_id' => 'Видео',
            'seek_to' => 'Начать с (сек)',
            'duration' => 'Продолжительность (сек)',
            'mute' => 'Отключить звук',
            'speed' => 'Скорость воспроизведения',
            'volume' => 'Громкость',
            'to_next_slide' => 'Автоматический переход на следующий слайд',
        ]);
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

    public function sourceIsFile(): bool
    {
        return (int) $this->source === VideoSource::FILE;
    }

    public function sourceIsYouTube(): bool
    {
        return (int) $this->source === VideoSource::YOUTUBE;
    }
}
