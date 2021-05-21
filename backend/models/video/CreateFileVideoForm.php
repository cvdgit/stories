<?php

namespace backend\models\video;

use common\models\SlideVideo;
use yii\web\UploadedFile;

class CreateFileVideoForm extends FileVideoForm
{

    public function init()
    {
        parent::init();
        $this->source = VideoSource::FILE;
    }

    public function createVideo(): int
    {
        if (!$this->validate()) {
            throw new \DomainException('Model not valid');
        }
        $model = new SlideVideo();
        $model->title = $this->title;
        $model->source = $this->source;
        $model->video_id = UploadedFile::getInstance($this, 'videoFile');
        $model->save();
        return $model->id;
    }

}