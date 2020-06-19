<?php

namespace backend\components\notification;

use common\components\StoryCover;
use common\models\Story;
use Yii;

class NewStoryAudioNotification extends Notification
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
        return 'Новая озвучка для истории: "' . $this->model->title . '". Включайте и наслаждайтесь.';
    }

    public function getImage(): string
    {
        return StoryCover::getListThumbPath($this->model->cover);
    }

}