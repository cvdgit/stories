<?php

namespace backend\components\notification;

use yii\helpers\Html;
use yii\helpers\Json;

abstract class Notification
{

    abstract public function getLink(): string;

    abstract public function getText(): string;

    abstract public function getImage(): string;

    public function render(): string
    {
        return Json::encode([
            'text' => $this->getText(),
            'link' => $this->getLink(),
            'image' => $this->getImage(),
        ]);
    }

}