<?php

namespace backend\models\links;

class BaseYoutubeLink extends BaseLink
{

    public $youtube_id;
    public $start;
    public $end;

    public function rules()
    {
        return array_merge([
            [['youtube_id', 'start', 'end'], 'required'],
            ['youtube_id', 'string', 'max' => 50],
            [['start', 'end'], 'integer'],
        ], parent::rules());
    }

    public function attributeLabels()
    {
        return array_merge([
            'youtube_id' => 'ID видео',
            'start' => 'Время начала в секундах',
            'end' => 'Время окончания в секундах',
        ], parent::attributeLabels());
    }

    protected function createHref(string $youtubeID, int $start, int $end): string
    {
        return sprintf('https://www.youtube.com/embed/%1$s?rel=0&start=%2$s&end=%3$s', $youtubeID, $start, $end);
    }

}