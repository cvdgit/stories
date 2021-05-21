<?php

namespace backend\models\video;

class FileVideoForm extends VideoForm
{

    public $videoFile;

    public function rules()
    {
        return array_merge([
            ['videoFile', 'file', 'extensions' => 'mp4'],
        ], parent::rules());
    }

    public function attributeLabels()
    {
        return array_merge([
            'videoFile' => 'Файл с видео',
        ], parent::attributeLabels());
    }

}