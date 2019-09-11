<?php


namespace backend\models\video;


use common\models\SlideVideo;
use yii\base\Model;

class CreateVideoForm extends Model
{

    public $title;
    public $video_id;

    public function rules()
    {
        return [
            [['video_id', 'title'], 'required'],
            [['video_id', 'title'], 'string', 'max' => 255],
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
        $model = SlideVideo::create($this->title, $this->video_id);
        return $model->save();
    }

}