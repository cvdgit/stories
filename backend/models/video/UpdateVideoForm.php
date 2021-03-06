<?php

namespace backend\models\video;

use common\models\SlideVideo;

class UpdateVideoForm extends YouTubeVideoForm
{

    public $model_id;

    private $_model;

    public function __construct(int $model_id, $config = [])
    {
        $this->model_id = $model_id;
        parent::__construct($config);
    }

    public function attributeLabels()
    {

        return [
            'title' => 'Название',
            'video_id' => 'ИД видео Youtube',
        ];
    }

    public function rules()
    {
        return [
            [['video_id', 'title'], 'required'],
            [['video_id', 'title'], 'string', 'max' => 255],
            [['source'], 'integer'],
            ['source', 'in', 'range' => VideoSource::getTypes()],
        ];
    }

    public function loadModel()
    {
        $model = $this->getModel();
        $this->title = $model->title;
        $this->video_id = $model->video_id;
    }

    public function getModel()
    {
        if ($this->_model === null) {
            $this->_model = SlideVideo::findModel($this->model_id);
        }
        return $this->_model;
    }

    public function saveVideo()
    {
        $model = $this->getModel();
        $model->title = $this->title;
        $model->video_id = $this->video_id;
        $model->save();
        return $model->id;
    }

}