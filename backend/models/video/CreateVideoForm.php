<?php

namespace backend\models\video;

use common\models\SlideVideo;
use yii\base\Model;

class CreateVideoForm extends Model
{

    public $title;
    public $video_id;
    public $source;

    public function init()
    {
        parent::init();
        $this->source = VideoSource::YOUTUBE;
    }

    public function rules()
    {
        return [
            [['video_id', 'title', 'source'], 'required'],
            [['video_id', 'title'], 'string', 'max' => 255],
            [['source'], 'integer'],
            ['source', 'in', 'range' => VideoSource::getTypes()],
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => 'Название',
            'video_id' => 'ИД видео Youtube',
        ];
    }

    public function createVideo()
    {
        if (!$this->validate()) {
            throw new \DomainException('Model not valid');
        }
        $model = SlideVideo::create($this->title, $this->video_id, $this->source);
        $model->save();
    }

}