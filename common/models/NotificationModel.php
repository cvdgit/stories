<?php

namespace common\models;

use yii\base\Model;

class NotificationModel extends Model
{

    public $text;

    public function rules()
    {
        return [
            ['text', 'string'],
        ];
    }

    public function createNotification()
    {
        if (!$this->validate()) {
            throw new \DomainException('Notification not valid');
        }
        $model = Notification::create($this->text);
        $model->save();
        return $model;
    }

}