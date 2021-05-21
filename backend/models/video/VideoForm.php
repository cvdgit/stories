<?php

namespace backend\models\video;

use yii\base\Model;

class VideoForm extends Model
{

    public $title;
    public $video_id;
    public $source;

    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title', 'video_id'], 'string', 'max' => 255],
            [['source'], 'integer'],
            ['source', 'in', 'range' => VideoSource::getTypes()],
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => 'Название',
        ];
    }

}