<?php

namespace backend\models\video;

class YouTubeVideoForm extends VideoForm
{

    public function attributeLabels()
    {
        return array_merge([
            'video_id' => 'ИД видео Youtube',
        ], parent::attributeLabels());
    }
}
