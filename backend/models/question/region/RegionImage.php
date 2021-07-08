<?php

namespace backend\models\question\region;

use common\models\StoryTestQuestion;
use Yii;

class RegionImage
{

    private $model;

    /** @var int */
    private $width = 0;
    /** @var int */
    private $height = 0;

    public function __construct(StoryTestQuestion $model)
    {
        $this->model = $model;

        $imagePath = $this->getImagePath();
        if ($imagePath !== '' && file_exists($imagePath)) {
            [$this->width, $this->height] = getimagesize($imagePath);
        }
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

    public function getOrigImageUrl(): string
    {
        if (empty($this->model->image)) {
            return '';
        }
        return $this->getImagesPath(false) . str_replace('thumb_', '', $this->model->image);
    }

    public function getImagePath(): string
    {
        if (empty($this->model->image)) {
            return '';
        }
        return $this->getImagesPath() . $this->model->image;
    }

    public function getOrigImagePath(): string
    {
        if (empty($this->model->image)) {
            return '';
        }
        return $this->getImagesPath() . str_replace('thumb_', '', $this->model->image);
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

}