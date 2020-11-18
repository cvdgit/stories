<?php

namespace backend\models\question;

use common\models\StoryTestQuestion;
use DomainException;

class UpdateRegionQuestion extends RegionQuestion
{

    private $model;

    public function __construct(StoryTestQuestion $model, $config = [])
    {
        $this->model = $model;
        $this->loadModelAttributes();
        parent::__construct($config);
    }

    private function loadModelAttributes()
    {
        foreach ($this->getAttributes() as $name => $value) {
            $modelAttributes = $this->model->getAttributes();
            if (isset($modelAttributes[$name])) {
                $this->{$name} = $this->model->{$name};
            }
        }
    }

    public function getImageUrl()
    {
        return $this->model->getImageUrl();
    }

    public function hasImage()
    {
        return !empty($this->model->image);
    }

    public function update()
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
        $this->uploadImage($this->model);
        $this->model->save();
    }

    public function getModelID()
    {
        return $this->model->id;
    }

}