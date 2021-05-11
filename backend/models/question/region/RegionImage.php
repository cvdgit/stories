<?php

namespace backend\models\question\region;

use common\models\StoryTestQuestion;
use Yii;

class RegionImage
{

    private $prefix = '_mini';
    private $model;

    public function __construct(StoryTestQuestion $model)
    {
        $this->model = $model;
    }

    public function getImagesPath(bool $abs = true): string
    {
        return ($abs ? Yii::getAlias('@public') : '') . Yii::$app->params['test.question.images'] . '/' . $this->model->story_test_id . '/';
    }

    public function getImageUrl(): string
    {
        if (empty($this->model->image)) {
            return '';
        }
        return $this->getImagesPath(false) . $this->model->image;
    }

    public function getImagePath(): string
    {
        if (empty($this->model->image)) {
            return '';
        }
        return $this->getImagesPath() . $this->model->image;
    }

    public function getOriginalImagePath(): string
    {
        if (empty($this->model->image)) {
            return '';
        }
        return $this->getImagesPath() . str_replace($this->prefix, '', $this->model->image);
    }

}