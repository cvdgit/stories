<?php

namespace backend\models\pass_test;

use yii\base\Model;

class CreatePassTestForm extends Model
{

    public $name;
    public $content;
    public $payload;

    public function init(): void
    {
        $this->name = 'Выберите правильный ответ из вариантов, предложенных в списке';
        parent::init();
    }

    public function rules(): array
    {
        return [
            [['name', 'content'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['content', 'payload'], 'safe'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Вопрос',
            'content' => 'Текст с пропусками',
        ];
    }
}
