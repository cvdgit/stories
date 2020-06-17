<?php

namespace backend\components\notification;

use common\components\StoryCover;
use common\helpers\SmartDate;
use common\models\Story;
use Yii;

class NewStoryNotification extends Notification
{

    protected $model;

    public function __construct(Story $model)
    {
        $this->model = $model;
    }

    public function getLink(): string
    {
        return Yii::$app->urlManagerFrontend->createAbsoluteUrl(['story/view', 'alias' => $this->model->alias]);
    }

    public function getText(): string
    {
        return 'Появилась новая история: "' . $this->model->title . '". Советуем ее посмотреть.';
    }

    public function getImage(): string
    {
        return StoryCover::getListThumbPath($this->model->cover);
    }

}