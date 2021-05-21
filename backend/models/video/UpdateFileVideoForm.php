<?php

namespace backend\models\video;

use common\models\SlideVideo;
use DomainException;
use yii\web\UploadedFile;

class UpdateFileVideoForm extends FileVideoForm
{

    private $model;

    public function __construct(SlideVideo $model, $config = [])
    {
        parent::__construct($config);
        $this->model = $model;
        $this->loadModelAttributes();
    }

    private function loadModelAttributes(): void
    {
        foreach ($this->getAttributes() as $name => $value) {
            $modelAttributes = $this->model->getAttributes();
            if (isset($modelAttributes[$name])) {
                $this->{$name} = $this->model->{$name};
            }
        }
    }

    public function updateVideo(): void
    {
        if (!$this->validate()) {
            throw new DomainException('Model not valid');
        }
        $modelAttributes = $this->model->getAttributes();
        foreach ($this->getAttributes() as $name => $value) {
            if (array_key_exists($name, $modelAttributes)) {
                $this->model->{$name} = $value;
            }
        }
        $this->model->video_id = UploadedFile::getInstance($this, 'videoFile');
        $this->model->save();
    }

    public function getVideoUrl()
    {
        return $this->model->getUploadedFileUrl('video_id');
    }

    public function sourceIsFile(): bool
    {
        return VideoSource::isFile($this->model);
    }

    public function sourceIsYouTube(): bool
    {
        return VideoSource::isYouTube($this->model);
    }

}